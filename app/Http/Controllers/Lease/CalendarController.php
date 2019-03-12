<?php

namespace App\Http\Controllers\Lease;

use App\Http\Requests\Calendar\CreateValidator;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\Lease;

/**
 * Class CalendarController
 * 
 * @package App\Http\Controllers\Lease
 */
class CalendarController extends Controller
{
    /**
     * Creer een nieuwe CalendarController instantie
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct(); // Initialiseer de globale constructor
        $this->middleware(['auth', 'role:admin|leiding|webmaster', 'forbid-banned-user']);
    }

    /**
     * Methode voor het calendar overzicht van de verhuringen. 
     * 
     * @param  Lease $lease De databank model entiteit voor de verhuringen. 
     * @return View
     */
    public function index(Lease $lease): View
    {
        return view('calendar.index', ['leases' => $lease->orderBy('start_datum', 'ASC')->simplePaginate()]);
    }

    /**
     * Method for displaying the create view for an new lease.
     *
     * @return View
     */
    public function create(): View
    {
        $statusses = (new Lease)->getLeaseStatusses();
        return view('calendar.create', compact('statusses'));
    }

    /**
     * Method for creating the lease in the calendar.
     *
     * @param  CreateValidator $input The form request class that handles the validation.
     * @return RedirectResponse
     */
    public function store(CreateValidator $input): RedirectResponse
    {
        if ($lease = Lease::create($input->all())) {
            $lease->logActivity("Heeft een verhuring toegevoegd in de applicatie.", 'Verhuringen');

            try { // To find the user or tenant in the application.
                $tenant = User::whereEmail($input->email)->firstOrFail();
            }

            // There is no tenant found so we create the tenant as a new one in the application.
            catch (ModelNotFoundException $exception) {
                $input->merge(['name' => "{$input->voornaam} {$input->achternaam}"]);
                $tenant = User::create($input->all())->assignRole('huurder');
            }

            // Attach the tenant to the inserted lease.
            $lease->tenant()->associate($tenant)->save();
        }

        return redirect()->route('calendar.index');
    }

    /**
     * Display a lease in the application. 
     * 
     * @param  Lease $lease De databnak entiteit van de verhuring in de applicatie.
     * @return View
     */
    public function show(Lease $lease): View
    {
        $statusses     = $lease->getLeaseStatusses();
        $cantEdit      = $this->auth->user()->cannot('update', $lease);
        $canViewTenant = $this->auth->user()->can('view', $lease->tenant);

        return view('calendar.show', compact('lease', 'statusses', 'cantEdit', 'canViewTenant'));
    }

    /**
     * Method for deleting a lease in the application.
     *
     * @throws \Exception When no record is found in the application.
     *
     * @param Request $request The instance that holds all the request information.
     * @param Lease   $lease   The database resource from the lease.
     * @return \Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, Lease $lease)
    {
        if ($request->isMethod('GET')) {
            return view('calendar.delete', compact('lease'));
        }

        if ($request->isMethod('DELETE') && $lease->delete()) {
            $this->flashMessage->success('De verhuring is verwijderd in de applicatie.');
            $lease->logActivity('Heeft een verhuring verwijderd in de applicatie.', 'Verhuringen');
        }

        return redirect()->route('calendar.index');
    }
}
