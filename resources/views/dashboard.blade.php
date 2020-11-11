@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mb-3">
            <div class="col-md-12">
                <booking-list :user="{{ auth()->user() }}"></booking-list>
            </div>
        </div>

        @if (auth()->user() !== null)
            <div class="row justify-content-center mb-3">
                <div class="col-md-12">
                    <a @click="showForm" class="btn btn-primary">Book a meeting room</a>
                </div>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col-md-12">
                    <booking-form :user="{{ auth()->user() }}"></booking-form>
                </div>
            </div>

        @endif
    </div>
@endsection