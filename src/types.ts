export interface Order {
    recipient: {
        name: string;
        address1: string;
        address2?: string;
        city: string;
        state_code: string;
        country_code: string;
        zip: string;
        phone?: string;
        email: string;
    };
    items: {
        sync_variant_id: number;
        quantity: number;
    }[];
    packing_slip?: {
        email: string;
        phone: string;
        message: string;
        logo_url: string;
        store_name: string;
    };
}

export interface Cart {
    items: { [id: number]: CartItem },
}

export interface CartItem {
    id: number,
    name: string,
    price: number,
    img?: string,
    quantity: number,
}