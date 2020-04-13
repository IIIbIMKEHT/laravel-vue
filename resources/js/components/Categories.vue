<template>
    <div class="container">

        <div class="col-md-12 mt-3" v-if="$gate.isAdminOrModer()">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Categories Table</h3>
                    <div class="card-tools">
                        <button class="btn btn-success" @click="newModal">
                            Add New
                            <i class="fas fa-user-plus fa-fw"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Registred at</th>
                            <th>Modify</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="category in categories.data" :key="category.id">
                            <td>{{category.id}}</td>
                            <td>{{category.title}}</td>
                            <td>{{category.created_at | myDate }}</td>
                            <td>
                                <a href="#" @click="editModal(category)">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                /
                                <a href="#" @click="deleteCategory(category.id)">
                                    <i class="fa fa-trash red"></i>
                                </a>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <pagination :data="categories" @pagination-change-page="getResults">
                        <span slot="prev-nav">&lt; Пред</span>
                        <span slot="next-nav">След &gt;</span>
                    </pagination>
                </div>
            </div>
            <!-- /.card -->
        </div>

        <div v-if="!$gate.isAdminOrModer()">
            <not-found></not-found>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="AddNew" tabindex="-1" role="dialog" aria-labelledby="AddNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{editMode ? 'Update Category' : 'Add New'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateCategory() : createCategory()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.title" type="text" name="title"
                                       placeholder="Title"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                <has-error :form="form" field="title"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="editMode" type="submit" class="btn btn-success">Update</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Categories",
        data() {
            return {
                editMode: false,
                categories: {},
                form: new Form({
                    id: '',
                    title: '',
                    created_at: ''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/category?page=' + page)
                    .then(response => {
                        this.categories = response.data;
                    });
            },
            editModal(category){
                this.editMode = true;
                this.form.clear();
                $('#AddNew').modal('show');
                this.form.fill(category);
            },
            newModal(){
                this.editMode = false;
                this.form.reset();
                $('#AddNew').modal('show');
            },
            deleteCategory(id){
                swal.fire({
                    title: 'Вы уверены?',
                    text: "Вы не сможете вернуть это!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Да, уверен!'
                }).then((result) => {
                    // Send request to the server
                    if (result.value) {
                        this.form.delete('api/category/'+id).then(()=>{
                            swal.fire(
                                'Удалена!',
                                'Категория успешно удалена!',
                                'success'
                            )
                            Fire.$emit('AfterCreate');
                        }).catch(()=> {
                            swal.fire("Ошибка!", "Произошла ошибка!", "warning");
                        });
                    }
                })
            },

            loadCategories(){
                if (this.$gate.isAdminOrModer()){
                    axios.get('api/category').then(({data}) => (this.categories = data));
                }
            },
            createCategory () {
                // Submit the form via a POST request
                this.form.post('api/category')
                    .then(({ data }) => {
                        console.log(data);
                        this.$Progress.start();
                        Fire.$emit('AfterCreate');
                        $('#AddNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Category created in successfully'
                        })
                        this.$Progress.finish();
                    });
            },
            updateCategory() {
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/category/'+this.form.id)
                    .then(() => {
                        // success
                        $('#AddNew').modal('hide');
                        swal.fire(
                            'Обновлена!',
                            'Успешно обновлена!',
                            'success'
                        )
                        Toast.fire({
                            icon: 'success',
                            title: 'Category updated in successfully'
                        })
                        this.$Progress.finish();
                        Fire.$emit('AfterCreate');
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            }
        },
        created() {
            Fire.$on('searching',() => {
                let query = this.$parent.search;
                axios.get('api/findCategory?q=' + query)
                    .then((data) => {
                        this.categories = data.data
                    })
                    .catch(() => {
                    })
            })
            this.loadCategories();
            Fire.$on('AfterCreate', () => {this.loadCategories()});
            // setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>

<style scoped>

</style>
