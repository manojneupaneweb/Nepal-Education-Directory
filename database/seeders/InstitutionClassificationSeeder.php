<?php

namespace Database\Seeders;

use App\Models\InstitutionCategory;
use App\Models\InstitutionType;
use Illuminate\Database\Seeder;

class InstitutionClassificationSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['School', 'विद्यालय', 'school', 1],
            ['College', 'कलेज', 'college', 2],
            ['University', 'विश्वविद्यालय', 'university', 3],
            ['Montessori', 'मोन्टेसोरी', 'montessori', 4],
            ['Early Childhood Development (ECD) Center', 'प्रारम्भिक बालविकास केन्द्र', 'ecd-center', 5],
            ['Technical Institute', 'प्राविधिक संस्थान', 'technical-institute', 6],
            ['Vocational Training Center', 'व्यावसायिक तालिम केन्द्र', 'vocational-training-center', 7],
            ['Educational Training Center', 'शैक्षिक तालिम केन्द्र', 'educational-training-center', 8],
        ];

        foreach ($types as [$name, $nameNe, $slug, $order]) {
            InstitutionType::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'name_ne' => $nameNe,
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $order,
                ],
            );
        }

        $categories = [
            ['Government School', 'सरकारी विद्यालय', 'government-school', null],
            ['Community School', 'सामुदायिक विद्यालय', 'community-school', null],
            ['Private School', 'निजी विद्यालय', 'private-school', null],
            ['Boarding School', 'आवासीय विद्यालय', 'boarding-school', null],
            ['Day School', 'दिनको विद्यालय', 'day-school', null],
            ['Science College', 'विज्ञान कलेज', 'science-college', null],
            ['Management College', 'व्यवस्थापन कलेज', 'management-college', null],
        ];

        foreach ($categories as $index => [$name, $nameNe, $slug, $parentSlug]) {
            $parentId = $parentSlug === null
                ? null
                : InstitutionCategory::query()->where('slug', $parentSlug)->value('id');

            InstitutionCategory::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'name_ne' => $nameNe,
                    'parent_id' => $parentId,
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
