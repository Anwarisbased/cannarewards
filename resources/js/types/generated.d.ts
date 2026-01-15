declare namespace App.Data {
export type TestDTO = {
name: string;
age: number;
isActive: boolean;
};
}
declare namespace App.Data.Tenant {
export type CommercialGoodData = {
id: number;
sku: string;
name: string;
description: string | null;
points_awarded: number;
image_url: string;
is_active: boolean;
};
}
