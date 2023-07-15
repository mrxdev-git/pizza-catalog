<template>
    <div>
        <h2>Ingredients</h2>
        <button @click="showModal = true" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#ingredientModal">Add Ingredient</button>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Cost Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ingredient in ingredients" :key="ingredient.id">
                    <td>{{ ingredient.name }}</td>
                    <td>{{ ingredient.price }}</td>
                    <td>
                        <button @click="deleteIngredient(ingredient.id)" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Ingredient Modal -->
        <div class="modal fade" id="ingredientModal" tabindex="-1" role="dialog" aria-labelledby="ingredientModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ingredientModalLabel">Add Ingredient</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ingredient-form @ingredient-added="fetchIngredients"></ingredient-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import IngredientForm from './IngredientForm.vue';
import axios from 'axios';

export default {
    components: {
        IngredientForm,
    },
    data() {
        return {
            ingredients: [],
            showModal: false,
        };
    },
    methods: {
        fetchIngredients() {
            axios.get('/api/ingredients')
                .then(response => {
                    this.ingredients = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        deleteIngredient(ingredientId) {
            axios.delete(`/api/ingredients/${ingredientId}`)
                .then(() => {
                    this.ingredients = this.ingredients.filter(i => i.id !== ingredientId);
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
    mounted() {
        this.fetchIngredients();
    },
};
</script>

<style>
/* Optional: Add custom styles here */
</style>
