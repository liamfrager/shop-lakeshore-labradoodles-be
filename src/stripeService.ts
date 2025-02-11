import dotenv from 'dotenv';
import Stripe from 'stripe';
import { Cart } from './types';
import PrintfulService from './printfulService';

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
            line_items: await StripeService.getLineItems(cart),
            mode: 'payment',
            shipping_address_collection: {'allowed_countries': ['US']},
            success_url: FE_DOMAIN + '/success',
            cancel_url: FE_DOMAIN + '/cart',
            metadata: metadata,
        }
        return this.stripe.checkout.sessions.create(checkoutSessionParams);
    }

    // Takes cart data in as an input and returns Stripe line items for placing an order.
    static async getLineItems(cart: Cart): Promise<Stripe.Checkout.SessionCreateParams.LineItem[]> {
        const lineItems = await Promise.all(Object.entries(cart.items).map(async ([id, item]) => {
            const variant = await PrintfulService.getVariant(Number(id));
            const lineItem: Stripe.Checkout.SessionCreateParams.LineItem = {
                price_data: StripeService.getPriceData(variant),
                quantity: item.quantity,
            }
            return lineItem;
        }));
        return lineItems;
    }

    
    // Takes a Printful variant as an input and returns a dictionary formatted as 'price-data' for the Stripe API session creation.
    static getPriceData(variant: any): Stripe.Checkout.SessionCreateParams.LineItem.PriceData {
        const priceData = {
            currency: variant.currency.toLowerCase(),
            unit_amount: parseInt(variant.retail_price.replace('.', '')),
            product_data: {
                name: variant.name,
                // TODO: Implement product descriptions
                description: variant.name,
                images: variant.files.reverse().map((file: any) => file.thumbnail_url),
            },
        }
        return priceData;
    }
}