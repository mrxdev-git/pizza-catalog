<template>
    <div>
        <h3>{{ pizza ? 'Edit Pizza' : 'Add Pizza' }}</h3>
        <form @submit.prevent="savePizza">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" class="form-control" v-model="form.name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Ingredients:</label>
                <div class="form-check" v-for="ingredient in ingredients" :key="ingredient.id">
                    <input class="form-check-input" type="checkbox" :id="`ingredient-${ingredient.id}`" v-model="form.ingredients" :value="ingredient.id">
                    <label class="form-check-label" :for="`ingredient-${ingredient.id}`">
                        {{ ingredient.name }} ({{ ingredient.price }})
                    </label>
                </div>
            </div>

            <div class="mb-3">
                Total Price: {{ totalPrice }} EUR
            </div>

            <button type="submit" class="btn btn-primary">{{ pizza ? 'Save' : 'Add' }}</button>
            <button type="button" class="btn btn-secondary" @click="closeForm" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['pizza', 'ingredients'],
    data() {
        return {
            form: {
                name: this.pizza ? this.pizza.name : '',
                ingredients: this.pizza ? this.pizza.ingredients.map(i => i.id) : [],
            },
            totalPrice: 0,
        };
    },
    methods: {
        savePizza() {
            if (this.pizza) {
                this.updatePizza();
            } else {
                this.addPizza();
            }
        },
        addPizza() {
            if (!this.form.ingredients.length){
                alert('Select ingredients');
                return false;
            }

            axios.post('/api/pizzas', this.form)
                .then(response => {
                    this.form.ingredients = response.data.ingredients.map(i => i.id);
                    this.$emit('pizza-added', response.data);
                    this.closeForm();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        updatePizza() {
            if (!this.form.ingredients.length){
                alert('Select ingredients');
                return false;
            }

            axios.put(`/api/pizzas/${this.pizza.id}`, this.form)
                .then(response => {
                    this.$emit('pizza-updated', response.data);
                    this.closeForm();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        closeForm() {
            this.form = {
                name: '',
                ingredients: [],
            };
        },

        calculateTotalPrice() {
            this.totalPrice = this.form.ingredients.reduce((total, ingredientId) => {
                const ingredient = this.ingredients.find(i => i.id === ingredientId);
                if (ingredient) {
                    return parseFloat(total) + parseFloat(ingredient.price);
                }
                return total;
            }, 0);
        }
    },
    watch: {
        form: {
            handler() {
                this.calculateTotalPrice();
            },
            deep: true
        }
    }
};
</script>

<style scoped>
/* Optional: Add custom styles here */
</style>
