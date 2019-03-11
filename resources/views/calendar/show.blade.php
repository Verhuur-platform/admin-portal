@extends ('layouts.app')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Verhuur kalender</h1>

        <div class="page-subtitle">
            Informatie overzicht
        </div>

            <div class="page-options d-flex">
                <a href="{{ route('calendar.index') }}" class="btn tw-rounded btn-sgv-green">
                    <i class="fe mr-1 fe-calendar"></i> Overzicht
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-3"> {{-- Side navigation --}}
            @include ('calendar.components.sidebar', ['lease' => $lease])
        </div> {{-- /// End Side navigation --}}
    
        <div class="col-9"> {{-- Information overview --}}
            <form class="card shadow-sm card-body">
                <h6 class="border-bottom border-gray pb-1 mb-3">Informatie overzicht</h6>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="inputStartDate">Start datum</label>
                        <input type="date" id="inputStartDate" class="form-control" readonly value="{{ $lease->start_datum->format('d-m-Y') }}">
                    </div>

                    <div class="form-group col-6">
                        <label for="inputEndDate">Eind datum</label>
                        <input type="date" id="inputEndDate" class="form-control" readonly value="{{ $lease->eind_datum->format('d-m-Y') }}">
                    </div>

                    <div class="form-group col-6">
                        <label for="inputPersonen">Aantal personen <span class="text-danger">*</span></label>
                    </div>
                </div>
            </form>
        </div> {{-- Information overview --}}
    </div>
@endsection