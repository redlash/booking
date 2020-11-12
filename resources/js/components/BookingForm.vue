
<template>
<div class="card">
    <div class="card-header" v-if="[null, undefined, ''].includes(record)"><h3>Book a meeting room</h3></div>
    <div class="card-header" v-if="![null, undefined, ''].includes(record)"><h3>Update a booking</h3></div>
    <div class="card-body">
        <form>
            <div class="form-group row" v-if="errorVisible">
                <div class="col-md-10 offset-1">
                    <div class="alert alert-danger" role="alert">
                        <span class="sr-only">Error:</span>
                        {{ error }}
                    </div>
                </div>
            </div>

            <div class="form-group row" v-if="messageVisible">
                <div class="col-md-10 offset-1">
                    <div class="alert alert-success" role="alert">
                        <span class="sr-only">Success:</span>
                        {{ message }}
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="meeting_room" class="col-md-3 col-form-label text-md-right">Select a meeting room</label>

                <div class="col-md-3">
                    <select v-model="state.meeting_room_id" @change="meetingRoomChanged"
                            id="meeting_room" class="form-control" name="meeting_room" value="" required>
                        <option value="">-- Please select --</option>
                        <option v-for="meetingRoom in meetingRooms" :value="meetingRoom.id">{{ meetingRoom.name }}</option>
                    </select>
                </div>

                <label for="date" class="col-md-3 col-form-label text-md-right">Please select a date</label>

                <div class="col-md-3">
                    <datepicker name="date" id="date"
                            v-model="state.occupy_at"
                                @selected="dateChanged"></datepicker>
                </div>
            </div>

            <div class="form-group row">
                <label for="start_at" class="col-md-3 col-form-label text-md-right">Time</label>

                <div class="col-md-3">
                    <vue-timepicker id="start_at"
                                    :hour-range="[[8, 17]]"
                                    :minute-interval="15"
                                    v-model="state.start_at">

                    </vue-timepicker>
                </div>

                <label for="end_at" class="col-md-3 col-form-label text-md-right">Duration</label>

                <div class="col-md-2">
                    <select v-model="duration"
                            id="end_at" class="form-control" name="end_at">
                        <option value="">-- Please select --</option>
                        <option value="30">30 Minutes</option>
                        <option value="60">60 Minutes</option>
                    </select>
                </div>

            </div>

            <div class="form-group row mb-2">
                <div class="col-md-12 offset-5">
                    <a @click="cancel" class="btn btn-warning">
                        Go back
                    </a>
                    <a @click="submit" class="btn btn-primary">
                        Save
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import VueTimepicker from 'vue2-timepicker'
    import 'vue2-timepicker/dist/VueTimepicker.css'


    export default {

        created() {

            const vm = this;

            vm.getMeetingRooms();
            vm.populate();
        },

        mounted() {
            //
        },

        props: ['user', 'record'],

        components: {
            Datepicker, VueTimepicker
        },

        data: function () {

            let d = new Date();
            if (d.getHours() > 17) {
                d.setDate(d.getDate() + 1)
            }
            let t =  {
                HH: null,
                mm: null,
                ss: '00'
            };

            return {
                state: {
                    meeting_room_id: '',
                    occupy_at: d,
                    start_at: t,
                    end_at: ''
                },
                meetingRooms: [],
                showRoomList: false,
                duration: '',
                errorVisible: false,
                messageVisible: false,
                message: '',
                error: ''
            }
        },

        methods: {

            populate: function () {

                const vm = this;

                if ([null, undefined, ''].includes(vm.record)) {
                    return;
                }

                vm.state.meeting_room_id = vm.record.meeting_room_id;
                /* Transform occupy_at from 'd/m/Y' to Date. */
                let [d, m, y] = vm.record.occupy_at.split('/').map(e => { return parseInt(e);});
                vm.state.occupy_at = new Date(y, m, d);
                /* Transform start_at to string. */
                let [h, mm] = vm.record.start_at.split(':');
                vm.state.start_at = {
                    HH: h,
                    mm: mm,
                    ss: '00'
                };
                vm.duration = vm.record.duration;
            },

            getMeetingRooms: function () {

                let vm = this;

                fetch('/api/meeting_rooms',{
                    mode: 'cors',
                    headers: {
                        'Authorization': `Bearer ${vm.user.api_token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        vm.meetingRooms = data;
                    })
            },

            /**
             * When meeting room changed, grab the available date.
             */
            meetingRoomChanged: function (event) {

                const vm = this;

                vm.dateDisabled = vm.state.meeting_room_id === '';
            },

            /**
             * When date changed, grab the available time slots for that date.
             *
             * @param event
             */
            dateChanged: function (event) {

                const vm = this;

                let d = vm.state.occupy_at;

                console.log('dateChanged');console.log(d);

                d = (typeof d === 'string')
                    ? d.split('/').join('')
                    : `${d.getFullYear()}${d.getMonth()+1}${d.getDate()}`;

                console.log(d);

                if (vm.state.meeting_room_id === '') {
                    vm.showRoomList = false;
                    return;
                }

                fetch(`/api/meeting_room/${vm.state.meeting_room_id}/${d}`,{
                    mode: 'cors',
                    headers: {
                        'Authorization': `Bearer ${vm.user.api_token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        vm.bookingRecords = data;
                    })
            },

            /**
             * Close the form and show the list.
             *
             * @param event
             */
            cancel: function (event) {

                const vm = this;
console.log('cancel')
                vm.$emit('canceled');
            },

            submit: function (event) {

                let vm = this;

                event.preventDefault();

                const payload = Object.assign({}, vm.state);
                payload.occupy_at = `${vm.state.occupy_at.getFullYear()}-${vm.state.occupy_at.getMonth()+1}-${vm.state.occupy_at.getDate()}`;
                payload.start_at = `${vm.state.start_at.HH}:${vm.state.start_at.mm}`;
                payload.end_at = vm.getEndAt(vm.state.start_at, vm.duration);
                vm.messageVisible = false;
                vm.errorVisible = false;

                let endpoint = `api/booking`,
                    method = 'post';
                if (![null, undefined, ''].includes(vm.record)) {
                    endpoint = `api/booking/${vm.record.id}`;
                    method = 'put';
                }

                fetch(endpoint, {
                    method: method,
                    mode: 'cors',
                    headers: {
                        'Authorization': `Bearer ${vm.user.api_token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                }).then(res => res.json())
                    .then(data => {
                        console.log(data);
                        if ('error' in data) {
                            data.error = data.error.includes('Integrity constraint')
                                ? 'Sorry, this room has been booked.'
                                : data.error;
                            vm.error = data.error;
                            vm.errorVisible = true;
                            return;
                        }

                        if ([null, undefined, ''].includes(vm.record)) {
                            vm.$emit('created');
                        } else {
                            vm.$emit('updated', data);
                        }
                    })
                    .catch(err => {
                        console.log(err);
                        vm.error = err;
                        vm.errorVisible = true;
                    })
            },

            getEndAt: function (startAt, minutes) {

                if ([undefined, null, ''].includes(startAt)) {
                    return 'Calculating...'
                }
                let d = new Date();
                d.setHours(parseInt(startAt.HH));
                d.setMinutes(parseInt(startAt.mm));
                d.setMinutes(d.getMinutes() + parseInt(minutes));

                return `${(d.getHours()<10?'0':'') + d.getHours()}:${(d.getMinutes()<10?'0':'') + d.getMinutes()}`
            }
        }
    }
</script>