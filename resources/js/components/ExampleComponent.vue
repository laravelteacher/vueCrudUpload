<template>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Upload Profile hj</div>
          <div class="card-body">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" :src="getPhoto()" alt="User profile picture">
                </div>
              </div>
            </div>
            <div class="tab-pane active" id="settings">
                <!-- this is form for add new data -->
              <form class="form-horizontal">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-10">
                    <input type="email" v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                    <has-error :form="form" field="name"></has-error> 
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSkills" class="col-sm-2 col-form-label">Avatar</label>
                  <div class="col-sm-10">
                      <!-- click on it for add new image -->
                    <input  ref="photo" type="file" class="form-control" name="photo" @change="update">
                    <has-error :form="form" field="photo"></has-error> 
                  </div>
                </div>
                <div class="form-group row">
                  <div class="offset-sm-2 col-sm-10">
                      <!-- click by this Button for save new data -->
                    <button type="submit" @click.prevent="SubmitPhoto" class="btn btn-danger">Submit</button>
                  </div>
                </div>
              </form>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <tbody>
                    <!-- show all datas here -->
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Photo</th>
                    <th>Modify</th>
                  </tr> 
                  <tr v-for="item in photos.data" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <!-- image that are in database show by this Code -->
                    <td><img :src="`img/${item.photo}`" class="profile-user-img img-fluid img-circle" style="height:40px; width:40px;"></td>
                    <td>
                        <!-- this Buttons are to remove or delete data -->
                      <a href="#" @click="editPhotoModal(item)">Edit</a>
                      <a href="#" @click="deletePhoto(item.id)">Remove</a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <!-- this is for show Paginate -->
              <pagination :data="photos" @pagination-change-page="getResults"></pagination>
            </div>
            <!-- this is modal to update Data -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="addNewLabel">Update Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <!-- this is Form for Update -->
                <form @submit.prevent="UpdatePhoto">
                  <div class="modal-body">
                    <div class="form-group">
                     <input v-model="form.name" type="text" name="name"
                      placeholder="Name"
                      class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                      <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group row">
                      <label for="inputSkills" class="col-sm-2 col-form-label">Avatar</label>
                      <div class="col-sm-10">
                        <input type="file" @change='update' name="photo" class="form-control" :class="{ 'is-invalid': form.errors.has('photo') }">
                        <has-error :form="form" field="photo"></has-error> 
                      </div>
                    </div>
                    <img class="profile-user-img img-fluid img-circle" :src="getPhoto()" alt="User profile picture">
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {

        data(){
            return {
                photos: {},
                form: new Form({
                    id: '',
                    name : '',
                    photo: ''
                })
            }
        },
        methods: {
// when User click an new image that image come here to convert as a data and show in Template befor insert in database
            update(e) {
                let file = e.target.files[0];
                let reader = new FileReader();  

                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.photo = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
            
            //For getting Instant Uploaded Photo
            getPhoto(){
               let photo = (this.form.photo.length > 100) ? this.form.photo : "img/"+ this.form.photo;
                return photo;
            },

            //Insert Photo
            SubmitPhoto(){
// the name is in rutes st
            this.form.post('st')
               .then(()=>{

                   Toast.fire({
                      icon: 'success',
                      title: 'File uploaded successfully'
                    })
                   this.loadTableData()
               })
               .catch(()=>{
                  console.log("Error.....")
               })

            },

            //get Table data post
            // get all data from database and to show in Template
            loadTableData(){

                axios.get('photo')
                   .then(({ data }) =>( this.photos = data))
                   .catch(()=>{
                      console.log("Error...")
                   })
            },
             listen() {
                Echo.private('photos')
                    .listen(alert('ali'));
            },
            //Pagination  this is for shoe Paginate
            getResults(page = 1) {
                  axios.get('photo?page=' + page)
                    .then(response => {
                      this.photos = response.data;
                  });
            },

            //Edit data by this function of Vue Js
            editPhotoModal(item){
               this.form.clear();
               this.form.reset();
               $('#addNew').modal('show');
               this.form.fill(item)
            },
// this is for update data ant put to Controller
            UpdatePhoto(id){
              this.form.put('update/'+this.form.id)
               .then(()=>{

                   Toast.fire({
                      icon: 'success',
                      title: 'Photo updated successfully'
                    })

                    this.loadTableData()

                    $('#addNew').modal('hide');
               })
               .catch(()=>{
                  console.log(".....")
               })
            },

            //Delete photo do by this function
            deletePhoto(id){
                Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                      
              if (result.value) {
                //Send Request to server
                this.form.delete('delete/'+id)
                    .then((response)=> {
                            Swal.fire(
                              'Deleted!',
                              'Photo deleted successfully',
                              'success'
                            )
                    this.loadTableData();

                    }).catch(() => {
                        Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: 'Something went wrong!',
                          footer: '<a href>Why do I have this issue?</a>'
                        })
                    })
                }

            })
            },
            
                  
        },

        created() {
          
          //LoadTableData   tabledata
          this.loadTableData()
            
        }
        
    }
</script>