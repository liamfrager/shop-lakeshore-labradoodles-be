import { Router } from 'express';
import PrintfulService from './printfulService';

const router = Router();

router.get('/products', async (req, res) => {
    try {
        const products = await PrintfulService.getAllProducts();
        res.json(products);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
});

router.get('/products/:id', async (req, res) => {
    try {
        const product = await PrintfulService.getProduct(Number(req.params.id));
        res.json(product);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
});

router.get('/variants/:id', async (req, res) => {
    try {
        const variant = await PrintfulService.getVariant(Number(req.params.id));
        res.json(variant);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
});

router.get('/products/:id/color', async (req, res) => {
    try {
        const colorCode = await PrintfulService.getColorCode(Number(req.params.id));
        res.json(colorCode);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
});

router.post('/orders', async (req, res) => {
    try {
        const order = req.body;
        const response = await PrintfulService.placeOrder(order);
        res.json(response);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
});

export default router;