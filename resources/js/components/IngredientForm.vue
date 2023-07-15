<template>
    <div>
        <form @submit.prevent="saveIngredient">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" class="form-control" v-model="form.name" required>
            </div>

            <div class="mb-3">
                <label for="cost-price" class="form-label">Cost Price:</label>
                <input type="number" id="cost-price" class="form-control" v-model="form.price" min="0" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">{{ ingredient ? 'Save' : 'Add' }}</button>
            <button type="button" class="btn btn-secondary" @click="closeForm" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
        </form>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['ingredient'],
        data() {
            return {
                form: {
                    name: this.ingredient ? this.ingredient.name : '',
                    price: this.ingredient ? this.ingredient.price : 0
                },
            };
        },
        methods: {
            saveIngredient() {
                if (this.ingredient) {
                    this.updateIngredient();
                } else {
                    this.addIngredient();
                }
            },
            addIngredient() {
                axios.post('/api/ingredients', this.form)
                    .then(response => {
                        this.$emit('ingredient-added', response.data);
                        this.closeForm();
                    })
                    .catch(error => {
                        console.error(error);
                    });
            },
            updateIngredient() {
                axios.put(`/api/ingredients/${this.ingredient.id}`, this.form)
                    .then(response => {
                        this.$emit('ingredient-updated', response.data);
                        this.closeForm();
                    })
                    .catch(error => {
                        console.error(error);
                    });
            },
            closeForm() {
                this.form = {
                    name: '',
                    price: 0
                };
            }
        },

        watch: {
            ingredient(v){
                if (v && v.id) {
                    this.form.name = v.name;
                    this.form.price = v.price;
                }
            }
        }
    };
</script>

<style scoped>
/* Optional: Add custom styles here */
</style>
