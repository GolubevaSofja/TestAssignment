<?php
// add-product.php

include '../classes/Product.php';
include '../classes/Book.php';
include '../classes/DVD.php';
include '../classes/Furniture.php';
include '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];


    $className = "classes\\$type";

    try {
        if (class_exists($className)) {
            $product = new $className($_POST);
            $product->saveToDatabase();
            header('Location: index.php');
            exit;
        } else {
            throw new Exception("Class not found: $className");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}


include '../views/header.php';
?>

<div id="app">
    <form method="POST" @submit.prevent="submitForm" id="product_form">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <h1>Add Product</h1>
            <div class="ms-auto">
                <button type="submit" class="btn btn-primary me-2">Save</button>
                <button type="button" class="btn btn-secondary" @click="cancelForm">Cancel</button>
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

            <!-- SKU -->
            <div class="mb-3 w-50">
                <label for="sku">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" v-model="form.sku" required>
            </div>

            <!-- Name -->
            <div class="mb-3 w-50">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" v-model="form.name" required>
            </div>

            <!-- Price -->
            <div class="mb-3 w-50">
                <label for="price">Price ($)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" v-model="form.price" required>
            </div>

            <!-- Product type -->
            <div class="mb-3 w-50">
                <label for="productType">Type</label>
                <select name="type" id="productType" class="form-control" v-model="form.type" required>
                    <option value="" disabled selected hidden>Please Choose...</option>
                    <option v-for="(type, key) in productTypes" :value="key">{{ key }}</option>
                </select>
                <span v-if="errors.type" class="text-danger">{{ errors.type }}</span>
            </div>

            <!-- Product type description -->
            <div class="mb-3 w-50" v-if="currentType && currentType.description">
                <p>{{ currentType.description }}</p>
            </div>

            <!-- Special attribute(s) of specific type -->
            <div v-for="(field, key) in currentType.fields" :key="key" class="mb-3 w-50">
                <label :for="key">{{ field.label }}</label>
                <input type="text" :id="key" :name="key" class="form-control" v-model="form[key]" :required="field.required">
                <span v-if="errors[key]" class="text-danger">{{ errors[key] }}</span>
            </div>

        </form>
    </div>
</div>

<script>
    Vue.use(Toasted, {
        theme: "bubble",
        duration: 1500
    });

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
            validateForm() {
                this.errors = {};

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
                    Vue.toasted.error('Please, provide the data of indicated type');
                    return;
                }

                const form = document.getElementById('product_form');

                form.submit();
            },
            cancelForm() {
                window.location.href = 'index.php';
            }
        }
    });
</script>

<?php include '../views/footer.php'; ?>
