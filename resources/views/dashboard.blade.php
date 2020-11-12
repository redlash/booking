@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mb-3">
            <div class="col-md-12">
                @if (auth()->user() !== null)
                <booking-list  v-if="listVisible" :user="{{ auth()->user() }}"
                               v-on:create="showNewForm"
                               v-on:edit="showEditForm">
                </booking-list>
                @else
                    <booking-list></booking-list>
                @endif
            </div>
        </div>

        @if (auth()->user() !== null)
            <div class="row justify-content-center mb-3">
                <div class="col-md-12">
                    <booking-form  v-if="formVisible" :user="{{ auth()->user() }}"
                                  :record="bookingRecord"
                                  v-on:created="handleCreated"
                                  v-on:updated="handleUpdated"
                                  v-on:canceled="handleCanceled">

                    </booking-form>
                </div>
            </div>

        @endif
    </div>
@endsection