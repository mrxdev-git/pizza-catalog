<template>
    <div>
        <h3>Add Ingredient</h3>
        <form @submit.prevent="addIngredient">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" class="form-control" v-model="form.name" required>
            </div>
            <div class="form-group">
                <label for="cost-price">Cost Price:</label>
                <input type="number" id="cost-price" class="form-control" v-model="form.price" min="0" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            form: {
                name: '',
                price: 0,
            },
        };
    },
    methods: {
        addIngredient() {
            axios.post('/api/ingredients', this.form)
                .then(() => {
                    this.$emit('ingredient-added');
                    this.form = {
                        name: '',
                        price: 0
                    };
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
};
</script>

<style scoped>
/* Optional: Add custom styles here */
</style>
