import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

interface Product {
    id: number;
    sku: string;
    name: string;
    points_awarded: number;
    msrp_cents: number;
    strain_type: string;
    image_url: string;
    is_active: boolean;
}

interface TeaserProps {
    product: Product;
}

export default function Teaser({ product }: TeaserProps) {
    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Head title={`Claim ${product.name}`} />

            <div className="flex-grow flex flex-col items-center justify-center p-4">
                <div className="max-w-md w-full space-y-6">
                    {/* Product Image */}
                    {product.image_url && (
                        <div className="rounded-xl overflow-hidden shadow-lg">
                            <img
                                src={product.image_url}
                                alt={product.name}
                                className="w-full h-64 object-cover"
                            />
                        </div>
                    )}

                    {/* Product Info */}
                    <div className="text-center">
                        <h1 className="text-2xl font-bold text-gray-900">{product.name}</h1>
                        <p className="text-gray-600 mt-2">Strain: {product.strain_type}</p>

                        {/* Points Display */}
                        <div className="mt-4 p-4 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg">
                            <p className="text-white text-lg font-semibold">+{product.points_awarded} Points</p>
                        </div>

                        <p className="text-gray-500 text-sm mt-2">
                            MSRP: ${(product.msrp_cents / 100).toFixed(2)}
                        </p>
                    </div>

                    {/* Claim Button */}
                    <div className="mt-8">
                        <Link href={`/auth/login?code=${product.sku}`}>
                            <Button className="w-full py-6 text-lg">
                                Claim Points
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}