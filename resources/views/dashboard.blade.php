@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mb-3"
             v-if="listExists" v-show="listVisible">
            <div class="col-md-12">
                <booking-list :user="{{ auth()->user() }}" v-on:edit="showEditForm"></booking-list>
            </div>
        </div>

        @if (auth()->user() !== null)
            <div class="row justify-content-center mt-3"
                 v-if="listExists" v-show="listVisible">
                <div class="col-md-12">
                    <a v-on:click="showNewForm" class="btn btn-primary">Book a meeting room</a>
                </div>
            </div>

            <div class="row justify-content-center mb-3" v-if="formVisible === true">
                <div class="col-md-12">
                    <booking-form :user="{{ auth()->user() }}"
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