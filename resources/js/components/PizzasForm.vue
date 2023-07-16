<template>
    <div>
        <form @submit.prevent="savePizza">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" class="form-control" v-model="form.name" required>
            </div>

            <div class="mb-3">
                <div class="selected" v-if="form.ingredients.length">
                    <label class="form-label">Selected ingredients:</label>

                    <draggable class="dragArea list-group w-full" :list="form.ingredients" handle=".handle">
                        <div class="form-check" v-for="ingredient_id in form.ingredients" :key="ingredient_id">
                            <span class="handle">
                                <font-awesome-icon icon="fa-solid fa-bars" />
                            </span>

                            <div>
                                <input class="form-check-input" type="checkbox" :id="`ingredient-${ingredient_id}`" v-model="form.ingredients" :value="ingredient_id">
                                <label class="form-check-label" :for="`ingredient-${ingredient_id}`">
                                    {{ getIngredientName(ingredient_id) }} ({{ getIngredientPrice(ingredient_id) }})
                                </label>
                            </div>
                        </div>
                    </draggable>
                </div>

                <div class="unselected" v-if="availableIngredients.length">
                    <label class="form-label">Available ingredients:</label>

                    <div class="form-check" v-for="ingredient in availableIngredients" :key="ingredient.id">
                        <input class="form-check-input" type="checkbox" :id="`ingredient-${ingredient.id}`" v-model="form.ingredients" :value="ingredient.id">
                        <label class="form-check-label" :for="`ingredient-${ingredient.id}`">
                            {{ ingredient.name }} ({{ ingredient.price }})
                        </label>
                    </div>
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
import { VueDraggableNext } from 'vue-draggable-next';

export default {
    props: ['pizza', 'ingredients'],
    components: {
        draggable: VueDraggableNext
    },
    data() {
        return {
            form: {
                name: this.pizza ? this.pizza.name : '',
                ingredients: this.pizza ? this.pizza.ingredients.map(i => i.id) : [],
            },
            totalPrice: 0
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
            let total = this.form.ingredients.reduce((total, ingredientId) => {
                const ingredient = this.ingredients.find(i => i.id === ingredientId);
                if (ingredient) {
                    return parseFloat(total) + parseFloat(ingredient.price);
                }
                return total;
            }, 0);

            this.totalPrice = (total + (total * 0.5)).toFixed(2);
        },

        getIngredientName(ingredientId) {
            const ingredient = this.ingredients.find(i => i.id === ingredientId);
            return ingredient ? ingredient.name : '';
        },

        getIngredientPrice(ingredientId) {
            const ingredient = this.ingredients.find(i => i.id === ingredientId);
            return ingredient ? ingredient.price : '';
        }
    },
    computed: {
        availableIngredients(){
            return this.ingredients.filter(i => {
                let existing = this.form.ingredients.find(ing => ing === i.id);
                return !existing;
            });
        }
    },
    watch: {
        form: {
            handler() {
                this.calculateTotalPrice();
            },
            deep: true
        },

        pizza(v){
            if (v && v.id){
                this.form.name = v.name;
                this.form.ingredients = v.ingredients.map(i => i.id);
                this.calculateTotalPrice();
            }
        }
    }
};
</script>

<style scoped>
.handle {
    float: left;
    margin-right: 2rem;
    font-size: 14px;
    margin-top: 2px;
    cursor: pointer;
    color: #6c6c6c
}
</style>
