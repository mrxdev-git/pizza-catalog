<template>
    <div>
        <h2>Ingredients</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#ingredientsModal">
            Add Ingredient
        </button>

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
                        <button @click="editIngredient(ingredient)" class="btn btn-primary">Edit</button>
                        <button @click="deleteIngredient(ingredient.id)" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Add/Edit Ingredient Modal -->
        <div class="modal" id="ingredientsModal" ref="ingredientsModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingIngredient ? 'Edit Ingredient' : 'Add Ingredient' }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ingredient-form :ingredient="editingIngredient" @ingredient-added="ingredientAdded" @ingredient-updated="ingredientUpdated"></ingredient-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import IngredientForm from './IngredientForm.vue';
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    components: {
        IngredientForm
    },
    data() {
        return {
            ingredients: [],
            editingIngredient: null,
            modal: {}
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
        ingredientAdded(ingredient) {
            this.ingredients.push(ingredient);
            this.modal.hide();
        },
        ingredientUpdated(ingredient) {
            const index = this.ingredients.findIndex(i => i.id === ingredient.id);
            if (index !== -1) {
                this.ingredients.splice(index, 1, ingredient);
            }

            this.modal.hide();
        },
        deleteIngredient(ingredientId) {
            if (confirm('Are you sure?')) {
                axios.delete(`/api/ingredients/${ingredientId}`)
                    .then(() => {
                        this.ingredients = this.ingredients.filter(i => i.id !== ingredientId);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        },
        editIngredient(ingredient) {
            this.modal.show();
            this.editingIngredient = ingredient;
        },
        resetIngredient(){
            this.editingIngredient = null;
        }
    },
    mounted() {
        this.modal = new Modal('#ingredientsModal');

        this.$refs.ingredientsModal.addEventListener('hidden.bs.modal', () => {
            this.resetIngredient();
        });

        this.fetchIngredients();
    },
};
</script>

<style>
/* Optional: Add custom styles here */
</style>
