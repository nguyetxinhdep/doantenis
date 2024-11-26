@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('HỆ THỐNG TENNIS') }}</div>
                
                <div class="card-body">
                    {{-- <div class="alert alert-success" role="alert">
                            id branch first {{ session('branch_active')->Name }}
                        </div>
                        @if (session('all_branch'))
                            <div class="alert alert-success">
                                OK: Chi nhánh ID là
                            </div>
                        @endif --}}
                    {{ __('Xin chào, bạn đã vào hệ thống!') }}
                </div>
            </div>
        </div>
    </div>
@endsection
