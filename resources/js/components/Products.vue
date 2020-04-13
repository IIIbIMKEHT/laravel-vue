<template>
    <div class="container">

        <div class="col-md-12 mt-3" v-if="$gate.isAdminOrModer()">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Products Table</h3>
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
                            <th>Category</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Img</th>
                            <th>New / Status</th>
                            <th>Price</th>
                            <th>Modify</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="product in products.data" :key="product.id">
                            <td>{{product.category.title}}</td>
                            <td>{{product.title}}</td>
                            <td>{{product.description}}</td>
                            <td><img width="150" height="150" :src="getProductPhoto(product.img)" alt="Product"></td>
                            <td>{{product.new == 1 ? 'Yes' : 'No'}} / {{product.status == 1 ? 'Yes' : 'No'}}</td>
                            <td>{{product.price }}</td>
                            <td>
                                <a href="#" @click="editModal(product)">
                                    <i class="fa fa-edit blue"></i>
                                </a>
                                /
                                <a href="#" @click="deleteProduct(product.id)">
                                    <i class="fa fa-trash red"></i>
                                </a>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <pagination :data="products" @pagination-change-page="getResults">
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
                        <h5 class="modal-title" id="exampleModalLabel">{{editMode ? 'Update Product' : 'Add New'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateProduct() : createProduct()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.title" type="text" name="title"
                                       placeholder="Title"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                <has-error :form="form" field="title"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="form.category_id" name="category_id" class="form-control">
                                    <option  :value="category.id" v-for="category in categories.data" :key="category.id">
                                        {{category.title}}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <textarea v-model="form.description" name="description"
                                       placeholder="description"
                                          class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                <has-error :form="form" field="description"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.price" type="text" name="price"
                                       placeholder="Price"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('price') }">
                                <has-error :form="form" field="price"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="file" name="file" @change="updateImg"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="new">New</label>
                                    <input v-model="form.new" type="checkbox" id="new" name="new">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <input v-model="form.status" type="checkbox" id="status" name="status">
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
        name: "Products",
        data() {
            return {
                editMode: false,
                products: {},
                categories: {},
                form: new Form({
                    id: '',
                    category_name: '',
                    category_id: '',
                    title: '',
                    description: '',
                    img: '',
                    price: '',
                    new: '',
                    status: '',
                    created_at: ''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/product?page=' + page)
                    .then(response => {
                        this.products = response.data;
                    });
            },
            getProductPhoto(item){
                return "img/products/"+ item ;
            },
            editModal(product){
                this.editMode = true;
                this.form.clear();
                $('#AddNew').modal('show');
                this.form.fill(product);
            },
            newModal(){
                this.editMode = false;
                this.form.reset();
                $('#AddNew').modal('show');
            },
            deleteProduct(id){
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
                        this.form.delete('api/product/'+id).then(()=>{
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
            updateImg(e){
                let file = e.target.files[0];
                let reader = new FileReader();
                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){
                    Toast.fire({
                        icon: 'error',
                        title: 'Слишком большой размер файла!'
                    })
                    this.form.img.reset();
                    return false;
                }
                reader.onloadend = (file) => {
                    this.form.img = reader.result;
                }
                reader.readAsDataURL(file);
            },
            loadCategories(){
                if (this.$gate.isAdminOrModer()){
                    axios.get('api/category').then(({data}) => (this.categories = data));
                }
            },
            loadProducts(){
                if (this.$gate.isAdminOrModer()){
                    axios.get('api/product').then(({data}) => (this.products = data));
                }
            },
            createProduct () {
                // Submit the form via a POST request
                this.form.post('api/product')
                    .then(({ data }) => {
                        console.log(data);
                        this.$Progress.start();
                        Fire.$emit('AfterCreate');
                        $('#AddNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Product created in successfully'
                        })
                        this.$Progress.finish();
                    });
            },
            updateProduct() {
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/product/'+this.form.id)
                    .then(() => {
                        // success
                        $('#AddNew').modal('hide');
                        swal.fire(
                            'Обновлен!',
                            'Успешно обновлен!',
                            'success'
                        )
                        Toast.fire({
                            icon: 'success',
                            title: 'Product updated in successfully'
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
                axios.get('api/findProduct?q=' + query)
                    .then((data) => {
                        this.products = data.data
                    })
                    .catch(() => {
                    })
            })
            this.loadCategories();
            this.loadProducts();
            Fire.$on('AfterCreate', () => {this.loadProducts()});
            // setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>

<style scoped>

</style>
