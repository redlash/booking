<template>

    <div class="card">
        <div class="card-header"><h3>Book a meeting room</h3></div>
        <div class="card-body">
            <form>

                <div class="form-group row">
                    <label for="meeting_room" class="col-md-2 col-form-label text-md-right">Select a meeting room</label>

                    <div class="col-md-4">
                        <select v-model="data.meeting_room_id" @changed="meetingRoomChanged"
                                id="meeting_room" class="form-control" name="meeting_room" value="" required>
                            <option value="">-- Please select --</option>
                            <option v-for="meetingRoom in meetingRooms" :value="meetingRoom.id">{{ meetingRoom.name }}</option>
                        </select>
                    </div>

                    <label for="date" class="col-md-2 col-form-label text-md-right">Please select a date</label>

                    <div class="col-md-4">
                        <input v-model="data.occupy_at" @changed="dateChanged"
                               id="date" type="text" class="form-control" name="date" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="start_at" class="col-md-2 col-form-label text-md-right">Select a start time</label>

                    <div class="col-md-4">
                        <select v-model="data.start_at" @changed="startTimeChanged"
                                id="start_at" class="form-control" name="start_at" required>
                            <option value="">-- Please select --</option>
                            <option v-for="time in availableTimes" :value="time">{{ time }}</option>
                        </select>
                    </div>

                    <label for="start_at" class="col-md-2 col-form-label text-md-right">Select a start time</label>

                    <div class="col-md-4">
                        <select v-model="data.end_at"
                                id="end_at" class="form-control" name="end_at" required>
                            <option value="">-- Please select --</option>
                            <option value="18:00">18:00</option>
                            <option value="18:30">18:30</option>
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
    export default {

        created() {

            this.getMeetingRooms();
        },

        mounted() {
            //console.log(this.user)
        },

        props: ['user'],

        data: function () {

            let d = new Date();

            return {
                data: {
                    meeting_room_id: '',
                    occupy_at: `${d.getFullYear()}-${d.getMonth()+1}-${d.getDate()}`,
                    start_at: '',
                    end_at: ''
                },
                meetingRooms: [],
                availableTimes: [
                    '8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30',
                    '12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30',
                    '16:00','16:30','17:00'
                ]
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

                let d = new Date();
                this.data.occupy_at = `${d.getFullYear()}-${d.getMonth()+1}-${d.getDate()}`;
                this.data.start_at = '';
                this.data.end_at = '';
            },

            /**
             * When date changed, grab the available time slots for that date.
             *
             * @param event
             */
            dateChanged: function (event) {
                //
            },

            startTimeChanged: function (event) {
                //
            },

            submit: function (event) {

                let vm = this;

                event.preventDefault();

                fetch(`api/booking`, {
                    method: 'post',
                    mode: 'cors',
                    headers: {
                        'Authorization': `Bearer ${vm.user.api_token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(vm.data)
                }).then(res => res.json())
                    .then(data => {
                        console.log(data);
                        //vm.records = data;
                    })
                    .catch(err => console.log(err))
            }
        }
    }
</script>