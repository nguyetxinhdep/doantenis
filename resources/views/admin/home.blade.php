@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    {{-- <div class="alert alert-success" role="alert">
                            id branch first {{ session('branch_active')->Name }}
                        </div>
                        @if (session('all_branch'))
                            <div class="alert alert-success">
                                OK: Chi nhánh ID là
                            </div>
                        @endif --}}
                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
@endsection
