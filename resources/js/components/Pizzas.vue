<template>
    <div>
        <h2>Pizzas</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#pizzaModal">
            Add Pizza
        </button>
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
                    <td>{{ calculateSellingPrice(pizza) }}</td>
                    <td>
                        <ul>
                            <li v-for="ingredient in pizza.ingredients" :key="ingredient.id">
                                {{ ingredient.name }} ({{ ingredient.price }})
                            </li>
                        </ul>
                    </td>
                    <td>
                        <button @click="editPizza(pizza)" class="btn btn-primary">Edit</button>
                        <button @click="deletePizza(pizza.id)" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Add/Edit Pizza Modal -->
        <div class="modal" id="pizzaModal" ref="pizzaModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingPizza ? 'Edit Pizza' : 'Add Pizza' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <pizza-form :pizza="editingPizza" :ingredients="ingredients" @pizza-added="pizzaAdded" @pizza-updated="pizzaUpdated"></pizza-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PizzaForm from './PizzasForm.vue';
import axios from 'axios';
import {Modal} from "bootstrap";

export default {
    components: {
        PizzaForm
    },
    data() {
        return {
            pizzas: [],
            ingredients: [],
            editingPizza: null,
            modal: {}
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
        calculateSellingPrice(pizza) {
            const ingredientsTotal = pizza.ingredients ? pizza.ingredients.reduce((total, ingredient) => {
                return total + ingredient.price;
            }, 0) : 0;
            const preparationCost = ingredientsTotal * 0.5;
            return (ingredientsTotal + preparationCost).toFixed(2);
        },
        pizzaAdded(pizza) {
            this.pizzas.push(pizza);
            this.modal.hide();
        },
        pizzaUpdated(pizza) {
            const index = this.pizzas.findIndex(p => p.id === pizza.id);
            if (index !== -1) {
                this.pizzas.splice(index, 1, pizza);
            }
            this.modal.hide();
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
        editPizza(pizza) {
            this.modal.show();
            this.editingPizza = pizza;
        },
        closeModal() {
            this.editingPizza = null;
        },
        resetPizza(){
            this.editingIngredient = null;
        }
    },
    mounted() {
        this.fetchPizzas();
        this.fetchIngredients();

        this.$refs.pizzaModal.addEventListener('hidden.bs.modal', () => {
            this.resetPizza();
        });

        this.modal = new Modal('#pizzaModal');
    },
};
</script>

<style>
/* Optional: Add custom styles here */
</style>
