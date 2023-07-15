<template>
    <div>
        <h3>Add Pizza</h3>
        <form @submit.prevent="addPizza">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" class="form-control" v-model="form.name" required>
            </div>
            <div class="form-group">
                <label>Ingredients:</label>
                <div class="form-check" v-for="ingredient in ingredients" :key="ingredient.id">
                    <input class="form-check-input" type="checkbox" :id="ingredient.id" :value="ingredient.id"
                           v-model="form.ingredients" @change="calculateTotalPrice">
                    <label class="form-check-label" :for="ingredient.id">
                        {{ ingredient.name }} ({{ ingredient.price }})
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="total-price">Total Price:</label>
                <input type="text" id="total-price" class="form-control" v-model="form.total_price" disabled>
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['ingredients'],
    data() {
        return {
            form: {
                name: '',
                ingredients: [],
                total_price: 0,
            },
        };
    },
    methods: {
        addPizza() {
            axios.post('/api/pizzas', this.form)
                .then(() => {
                    this.$emit('pizza-added');
                    this.form = {
                        name: '',
                        ingredients: [],
                        total_price: 0,
                    };
                })
                .catch(error => {
                    console.error(error);
                });
        },
        calculateTotalPrice() {
            const selectedIngredients = this.ingredients.filter(i => this.form.ingredients.includes(i.id));
            this.form.total_price = selectedIngredients.reduce((acc, ingredient) => {
                return acc + parseFloat(ingredient.price);
            }, 0);
        },
    },
};
</script>

<style scoped>
/* Optional: Add custom styles here */
</style>
