<template>
    <div>
        <h2>Pizzas</h2>
        <button @click="showModal = true" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#pizzaModal">Add Pizza</button>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Selling Price</th>
                    <th>Ingredients</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="pizza in pizzas" :key="pizza.id">
                    <td>{{ pizza.name }}</td>
                    <td>{{ pizza.price }}</td>
                    <td>
                        <ul>
                            <li v-for="ingredient in pizza.ingredients" :key="ingredient.id">{{ ingredient.name }}</li>
                        </ul>
                    </td>
                    <td>
                        <button @click="deletePizza(pizza.id)" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pizza Modal -->
        <div class="modal fade" id="pizzaModal" tabindex="-1" role="dialog" aria-labelledby="pizzaModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pizzaModalLabel">Add Pizza</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <pizza-form :ingredients="ingredients" @pizza-added="fetchPizzas"></pizza-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PizzaForm from './PizzasForm.vue';
import axios from 'axios';

export default {
    components: {
        PizzaForm,
    },
    data() {
        return {
            pizzas: [],
            ingredients: [],
            showModal: false,
        };
    },
    methods: {
        fetchPizzas() {
            axios.get('/api/pizzas')
                .then(response => {
                    this.pizzas = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        fetchIngredients() {
            axios.get('/api/ingredients')
                .then(response => {
                    this.ingredients = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        deletePizza(pizzaId) {
            axios.delete(`/api/pizzas/${pizzaId}`)
                .then(() => {
                    this.pizzas = this.pizzas.filter(p => p.id !== pizzaId);
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
    mounted() {
        this.fetchPizzas();
        this.fetchIngredients();
    },
};
</script>

<style>
/* Optional: Add custom styles here */
</style>
