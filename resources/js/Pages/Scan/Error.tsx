import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

interface ErrorProps {
    reason: string;
}

export default function Error({ reason }: ErrorProps) {
    let errorMessage = '';
    let errorTitle = '';

    switch (reason) {
        case 'used':
            errorTitle = 'Code Already Used';
            errorMessage = 'This QR code has already been claimed. Each code can only be used once.';
            break;
        case 'invalid':
        default:
            errorTitle = 'Invalid Code';
            errorMessage = 'The QR code you scanned is invalid or has been deactivated.';
            break;
    }

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Head title={errorTitle} />

            <div className="flex-grow flex flex-col items-center justify-center p-4">
                <div className="max-w-md w-full space-y-6 text-center">
                    <div className="bg-red-100 text-red-800 p-4 rounded-lg">
                        <h1 className="text-2xl font-bold">{errorTitle}</h1>
                    </div>

                    <p className="text-gray-600">{errorMessage}</p>

                    <div className="mt-8">
                        <Link href="/">
                            <Button variant="outline" className="w-full">
                                Back to Home
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}