<?php
// add-product.php

use classes\Product;
use classes\DVD;
use classes\Book;
use classes\Database;
use classes\Furniture;

include '../classes/Product.php';
include '../classes/Book.php';
include '../classes/DVD.php';
include '../classes/Furniture.php';
include '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];

    // Ensure the class name includes the namespace
    $className = "classes\\$type";

    try {
        if (class_exists($className)) {
            // Create a product instance with POST data
            $product = new $className($_POST);
            // Save the product to the database
            $product->saveToDatabase();
            // Redirect to index page
            header('Location: index.php');
            exit;
        } else {
            throw new Exception("Class not found: $className");
        }
    } catch (Exception $e) {
        // Handle exceptions, such as duplicate SKU errors
        $errorMessage = $e->getMessage();
    }
}


include '../views/header.php';
?>

<div id="app">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Add Product</a>
            <div class="ms-auto">
                <!-- button place -->

<!--                <button type="submit" class="btn btn-primary">Save</button>-->
<!--                <button type="button" class="btn btn-secondary" @click="cancelForm">Cancel</button>-->
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <!-- Display error message if exists -->
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <form method="POST" @submit.prevent="submitForm" id="product_form">
            <div class="mb-3 w-50">
                <label for="sku">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" v-model="form.sku" required>
                <span v-if="errors.sku" class="text-danger">{{ errors.sku }}</span>
            </div>
            <div class="mb-3 w-50">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" v-model="form.name" required>
                <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
            </div>
            <div class="mb-3 w-50">
                <label for="price">Price ($)</label>
                <input type="text" name="price" id="price" class="form-control" v-model="form.price" required>
                <span v-if="errors.price" class="text-danger">{{ errors.price }}</span>
            </div>

            <div class="mb-3 w-50">
                <label for="productType">Type</label>
                <select name="type" id="productType" class="form-control" v-model="form.type" @change="updateTypeSpecificFields" required>
                    <option value="" disabled selected hidden>Please Choose...</option>
                    <option v-for="(type, key) in productTypes" :value="key">{{ key }}</option>
                </select>
                <span v-if="errors.type" class="text-danger">{{ errors.type }}</span>
            </div>

            <div class="mb-3 w-50" v-if="currentType && currentType.description">
                <p>{{ currentType.description }}</p>
            </div>

            <div v-for="(field, key) in currentType.fields" :key="key" class="mb-3 w-50">
                <label :for="key">{{ field.label }}</label>
                <input type="text" :id="key" :name="key" class="form-control" v-model="form[key]" :required="field.required">
                <span v-if="errors[key]" class="text-danger">{{ errors[key] }}</span>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" @click="cancelForm">Cancel</button>
            </div>

        </form>
    </div>
</div>

<script>
    new Vue({
        el: '#app',
        data: {
            form: {
                sku: '',
                name: '',
                price: '',
                type: '',
                size: '',
                weight: '',
                height: '',
                width: '',
                length: ''
            },
            productTypes: {
                'DVD': {
                    description: 'Please, provide size in MB',
                    fields: {
                        size: { label: 'Size (MB)', required: true, type: 'number' }
                    }
                },
                'Book': {
                    description: 'Please, provide weight in Kg',
                    fields: {
                        weight: { label: 'Weight (Kg)', required: true, type: 'number' }
                    }
                },
                'Furniture': {
                    description: 'Please, provide dimensions in HxWxL format (cm)',
                    fields: {
                        height: { label: 'Height (cm)', required: true, type: 'number' },
                        width: { label: 'Width (cm)', required: true, type: 'number' },
                        length: { label: 'Length (cm)', required: true, type: 'number' }
                    }
                }
            },
            errors: {}
        },
        computed: {
            currentType() {
                return this.productTypes[this.form.type] || { fields: {} };
            }
        },
        methods: {
            updateTypeSpecificFields() {
                Object.keys(this.form).forEach(key => {
                    if (key !== 'sku' && key !== 'name' && key !== 'price' && key !== 'type') {
                        this.form[key] = '';
                    }
                });
                this.errors = {};
            },
            validateForm() {
                this.errors = {};

                if (!this.form.sku) this.errors.sku = 'SKU is required';
                if (!this.form.name) this.errors.name = 'Name is required';
                if (!this.form.price || isNaN(this.form.price)) this.errors.price = 'Price must be a valid number';

                Object.keys(this.currentType.fields).forEach(key => {
                    const field = this.currentType.fields[key];
                    if (field.required && !this.form[key]) {
                        this.errors[key] = `${field.label} is required`;
                    } else if (field.type === 'number' && isNaN(this.form[key])) {
                        this.errors[key] = `${field.label} must be a valid number`;
                    }
                });

                return Object.keys(this.errors).length === 0;
            },
            submitForm() {
                if (!this.validateForm()) {
                    alert('Please, provide the data of indicated type');
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';

                Object.keys(this.form).forEach(key => {
                    if (this.form[key] !== '') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.id = key;
                        input.value = this.form[key];
                        form.appendChild(input);
                    }
                });

                document.body.appendChild(form);
                form.submit();
            },
            cancelForm() {
                window.location.href = 'index.php';
            }
        }
    });
</script>

<?php include '../views/footer.php'; ?>
