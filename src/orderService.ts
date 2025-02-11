import Stripe from "stripe";
import PrintfulService from "./printfulService";
import { Order } from "./types";

export default class OrderService {

    // Takes a stripe.checkout.Session object as an input and places an order on Printful. Returns Printful's API response
    static async placeOrder(checkoutSession: Stripe.Checkout.Session) {
        const order: Order = {
            recipient: {
                name: checkoutSession.shipping_details?.name ?? 'CUSTOMER NAME',
                address1: checkoutSession.shipping_details?.address?.line1 ?? 'ADDRESS',
                address2: checkoutSession.shipping_details?.address?.line2 ?? '',
                city: checkoutSession.shipping_details?.address?.city ?? 'CITY',
                state_code: checkoutSession.shipping_details?.address?.state ?? 'STATE',
                country_code: checkoutSession.shipping_details?.address?.country ?? 'COUNTRY',
                zip: checkoutSession.shipping_details?.address?.postal_code ?? 'ZIP CODE',
                phone: checkoutSession.customer_details?.phone ?? 'PHONE NUMBER',
                email: checkoutSession.customer_details?.email ?? 'EMAIL',
            },
            items: Object.entries(checkoutSession.metadata || {}).map(([id, quantity]) => ({
                sync_variant_id: Number(id),
                quantity: Number(quantity),
            })),
            packing_slip: {
                email: 'lakeshorelabradoodles@gmail.com',
                phone: '+1(860)478-0267',
                message: 'Thank you for your purchase!',
                logo_url: 'https://shop.lakeshorelabradoodles.com/static/images/logo.png',
                store_name: 'Lakeshore Labradoodles',
            },
        }
        return PrintfulService.placeOrder(order);
    }
}
