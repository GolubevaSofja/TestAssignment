<?php

ob_start();

use classes\Product;

require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Book.php';
require_once '../classes/DVD.php';
require_once '../classes/Furniture.php';

try {
    $products = Product::getAllProducts();
} catch (Exception $e) {
}

include '../views/header.php';
?>

<div id="app">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <h1>Product List</h1>
            <div class="ms-auto">
                <a href="add-product.php" class="btn btn-primary me-2">ADD</a>
                <button @click="deleteSelected" class="btn btn-danger" id="delete-product-btn">MASS DELETE</button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        <form id="product-list" method="POST">
            <div class="row">
                <div class="col-md-3 mb-4" v-for="product in products" :key="product.sku">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <label class="d-block text-start">
                                <input type="checkbox" class="delete-checkbox me-2"
                                       v-model="selectedProducts" :value="product.sku">
                            </label>
                            <h5 class="card-title mt-3">{{ product.sku }}</h5>
                            <p class="card-text">{{ product.name }}</p>
                            <p class="card-text">${{ product.price }}</p>
                            <!-- Display specific attribute based on the product type -->
                            <p class="card-text">{{ getProductSpecificAttribute(product) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    new Vue({
        el: '#app',
        data: {
            products: <?php echo json_encode($products); ?>,
            selectedProducts: [],
            productAttributes: {
                'DVD': {
                    label: 'Size',
                    format: product => `${product.size} MB`
                },
                'Book': {
                    label: 'Weight',
                    format: product => `${product.weight} Kg`
                },
                'Furniture': {
                    label: 'Dimensions',
                    format: product => `${product.height}x${product.width}x${product.length} (CM)`
                }
            }
        },
        methods: {
            getProductSpecificAttribute(product) {
                const typeConfig = this.productAttributes[product.type];
                return typeConfig ? `${typeConfig.label}: ${typeConfig.format(product)}` : '';
            },
            deleteSelected() {
                if (this.selectedProducts.length === 0) {
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';

                this.selectedProducts.forEach(sku => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'skus[]';
                    input.value = sku;
                    form.appendChild(input);
                });

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                form.appendChild(actionInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    });
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $skus = $_POST['skus'] ?? [];

    if (!empty($skus)) {
        $deleted = Product::deleteProductsBySkus($skus);
        header('Location: index.php');
        exit;
    } else {
        ob_end_clean();
        header('Location: index.php');
        exit;
    }
}

ob_end_flush();

include '../views/footer.php';
?>
