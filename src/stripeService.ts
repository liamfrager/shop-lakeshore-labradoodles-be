import dotenv from 'dotenv';
import Stripe from 'stripe';
import { Cart } from './types';
import OrderService from './orderService';

dotenv.config();
const STRIPE_API_KEY = process.env.STRIPE_API_KEY!;
const FE_DOMAIN = process.env.FE_DOMAIN!;

export default class StripeService {

    private static stripe: Stripe = new Stripe(STRIPE_API_KEY);

    static async createCheckoutSession(cart: Cart): Promise<Stripe.Checkout.Session> {
        const metadata = Object.entries(cart.items).reduce((acc, [id, item]) => {
            acc[Number(id)] = item.quantity;
            return acc;
        }, {} as { [key: number]: number });

        const checkoutSessionParams: Stripe.Checkout.SessionCreateParams = {
            line_items: await OrderService.getLineItems(cart),
            mode: 'payment',
            shipping_address_collection: {'allowed_countries': ['US']},
            success_url: FE_DOMAIN + '/success',
            cancel_url: FE_DOMAIN + '/cart',
            metadata: metadata,
        }
        return this.stripe.checkout.sessions.create(checkoutSessionParams);
    }

    
    // Takes a Printful variant as an input and returns a dictionary formatted as 'price-data' for the Stripe API session creation
    static getPriceData(variant: any): Stripe.Checkout.SessionCreateParams.LineItem.PriceData {
        const priceData = {
            currency: variant.currency.toLowerCase(),
            unit_amount: parseInt(variant.retail_price.replace('.', '')),
            product_data: {
                name: variant.name,
                // TODO: Implement product descriptions
                description: variant.name,
                images: variant.files.map((file: any) => file.thumbnail_url),
            },
        }
        return priceData;
    }
}