import axios from 'axios';
import dotenv from 'dotenv';
import { Order } from './types';

dotenv.config();
const PRINTFUL_AUTH_TOKEN = process.env.PRINTFUL_AUTH_TOKEN;
const AUTO_FULFILL_PRINTFUL_ORDERS = process.env.AUTO_FULFILL_PRINTFUL_ORDERS === 'True';

export default class PrintfulService {
    private static authToken = PRINTFUL_AUTH_TOKEN;
    private static apiEndpoint = 'https://api.printful.com';
    private static apiHeaders = {
        'Authorization': 'Bearer ' + this.authToken
    };

    // Returns all sync products from the Printful shop.
    public static async getAllProducts() {
        const response = await axios.get(`${this.apiEndpoint}/sync/products`, {
            headers: this.apiHeaders,
            params: {status: 'all'},
        });
        return response.data.result;
    }

    // Takes a Printful sync product ID as an input and returns details on the product
    public static async getProduct(id: number) {
        const response = await axios.get(`${this.apiEndpoint}/sync/products/${id}`, {
            headers: this.apiHeaders,
            params: {limit: 100},
        });
        return response.data.result;
    }

    // Takes a Printful sync product ID as an input and returns details on all its variants
    public static async getVariantIDs(id: number) {
        const product = await this.getProduct(id);
        const variantsIDs = product.sync_variants.map((variant: { id: number }) => variant.id);
        return variantsIDs;
    }

    // Takes a Printful sync variant ID as an input and returns details on that variant
    public static async getVariant(id: number) {
        const response = await axios.get(`${this.apiEndpoint}/sync/variant/${id}`, {
            headers: this.apiHeaders,
        });
        return response.data.result.sync_variant;
    }

    // Takes a Printful product variant ID as an input and returns the color code associated with that variant
    public static async getColorCode(id: number) {
        const response = await axios.get(`${this.apiEndpoint}/products/variant/${id}`, {
            headers: this.apiHeaders,
        });
        return response.data.result.variant.color_code;
    }

    // Takes Printful order data as an input and places an order. Returns an API response from Printful
    public static async placeOrder(order: Order) {
        const response = await axios.post(`${this.apiEndpoint}/orders`, {
            headers: this.apiHeaders,
            body: order,
            params: {'confirm': AUTO_FULFILL_PRINTFUL_ORDERS},
        });
        return response.data;
    }

}