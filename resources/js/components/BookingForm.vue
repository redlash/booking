<template>

    <div class="card">
        <div class="card-header"><h3>Book a meeting room</h3></div>
        <div class="card-body">
            <form>
                <div class="form-group row">
                    <label for="meeting_room" class="col-md-2 col-form-label text-md-right">Select a meeting room</label>

                    <div class="col-md-4">
                        <select v-model="state.meeting_room_id" @change="meetingRoomChanged"
                                id="meeting_room" class="form-control" name="meeting_room" value="" required>
                            <option value="">-- Please select --</option>
                            <option v-for="meetingRoom in meetingRooms" :value="meetingRoom.id">{{ meetingRoom.name }}</option>
                        </select>
                    </div>

                    <label for="date" class="col-md-2 col-form-label text-md-right">Please select a date</label>

                    <div class="col-md-4">
                        <datepicker name="date" id="date"
                                v-model="state.occupy_at"
                                    @selected="dateChanged"></datepicker>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="start_at" class="col-md-2 col-form-label text-md-right">Time</label>

                    <div class="col-md-4">
                        <vue-timepicker id="start_at"
                                        :hour-range="[[8, 17]]"
                                        :minute-interval="15"
                                        v-model="state.start_at" @change="startTimeChanged">

                        </vue-timepicker>
                    </div>



                    <label for="end_at" class="col-md-2 col-form-label text-md-right">Duration</label>

                    <div class="col-md-4">
                        <select v-model="duration"
                                id="end_at" class="form-control" name="end_at">
                            <option value="">-- Please select --</option>
                            <option value="30">30 Minutes</option>
                            <option value="60">60 Minutes</option>
                        </select>
                    </div>

                </div>

                <div class="form-group row mb-2">
                    <div class="col-md-12 offset-6">
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

            this.getMeetingRooms();
        },

        mounted() {
            //console.log(this.user)
        },

        props: ['user'],

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
                showEndTime: false,
                duration: ''
            }
        },

        methods: {

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
                d = `${d.getFullYear()}${d.getMonth()+1}${d.getDate()}`;
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
                        vm.showRoomList = true;
                        vm.timeDisabled = false;
                    })
            },

            startTimeChanged: function (event) {

                const vm = this;

                vm.showEndTime = true;
            },

            submit: function (event) {

                let vm = this;

                event.preventDefault();
                const payload = Object.assign({}, vm.state);
                payload.occupy_at = `${vm.state.occupy_at.getFullYear()}-${vm.state.occupy_at.getMonth()+1}-${vm.state.occupy_at.getDate()}`;
                payload.start_at = `${vm.state.start_at.HH}:${vm.state.start_at.mm}`;
                payload.end_at = vm.getEndAt(vm.state.start_at, vm.duration);

                fetch(`api/booking`, {
                    method: 'post',
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
                        //vm.records = data;
                    })
                    .catch(err => console.log(err))
            },

            getEndAt: function (startAt, minutes) {

                if ([undefined, null, ''].includes(startAt)) {
                    return 'Calculating...'
                }
                let m = parseInt(startAt.mm) + parseInt(minutes);
                let h = parseInt(startAt.HH) + Math.trunc(m/60);
                console.log(`m: ${m}, h: ${h}`)
                return `${h}:${m%60}`
            }
        }
    }
</script>