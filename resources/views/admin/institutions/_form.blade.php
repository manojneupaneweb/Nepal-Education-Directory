@csrf
@if ($institution->exists)
    @method('PUT')
@endif

<div class="grid gap-4 md:grid-cols-2">
    <label class="block text-sm font-semibold">English name
        <input name="name" value="{{ old('name', $institution->name) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('name')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Nepali name
        <input name="name_ne" value="{{ old('name_ne', $institution->name_ne) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
    </label>
    <label class="block text-sm font-semibold">Short name
        <input name="short_name" value="{{ old('short_name', $institution->short_name) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
    </label>
    <label class="block text-sm font-semibold">Slug
        <input name="slug" value="{{ old('slug', $institution->slug) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Leave blank to generate from the English name">
        @error('slug')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Institution type
        <select name="institution_type_id" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
            <option value="">Select a type</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}" @selected(old('institution_type_id', $institution->institution_type_id) == $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
        @error('institution_type_id')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Parent institution ID
        <input name="parent_institution_id" value="{{ old('parent_institution_id', $institution->parent_institution_id) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('parent_institution_id')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold md:col-span-2">Categories
        <select name="category_ids[]" multiple class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_ids', $institution->exists ? $institution->categories->pluck('id')->all() : [])))>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_ids')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Ownership
        <select name="ownership_type" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
            <option value="">Unknown / not set</option>
            @foreach (App\Enums\OwnershipType::cases() as $ownership)
                <option value="{{ $ownership->value }}" @selected(old('ownership_type', $institution->ownership_type?->value) === $ownership->value)>{{ str_replace('_', ' ', $ownership->value) }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm font-semibold">Management
        <select name="management_type" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
            <option value="">Not set</option>
            @foreach (App\Enums\ManagementType::cases() as $management)
                <option value="{{ $management->value }}" @selected(old('management_type', $institution->management_type?->value) === $management->value)>{{ str_replace('_', ' ', $management->value) }}</option>
            @endforeach
        </select>
    </label>
    <label class="block text-sm font-semibold">Province ID
        <input name="province_id" value="{{ old('province_id', $institution->province_id) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('province_id')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">District ID
        <input name="district_id" value="{{ old('district_id', $institution->district_id) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
    </label>
    <label class="block text-sm font-semibold">Municipality ID
        <input name="municipality_id" value="{{ old('municipality_id', $institution->municipality_id) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('municipality_id')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Ward ID
        <input name="ward_id" value="{{ old('ward_id', $institution->ward_id) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
    </label>
    <label class="block text-sm font-semibold">Established year
        <input name="established_year" value="{{ old('established_year', $institution->established_year) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('established_year')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Registration number
        <input name="registration_number" value="{{ old('registration_number', $institution->registration_number) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
    </label>
    <label class="block text-sm font-semibold">Registration authority
        <input name="registration_authority" value="{{ old('registration_authority', $institution->registration_authority) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
    </label>
    <label class="block text-sm font-semibold">Official website
        <input name="official_website" value="{{ old('official_website', $institution->official_website) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('official_website')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Latitude
        <input name="latitude" value="{{ old('latitude', $institution->latitude) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('latitude')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold">Longitude
        <input name="longitude" value="{{ old('longitude', $institution->longitude) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
        @error('longitude')<span class="mt-1 block font-normal text-red-700">{{ $message }}</span>@enderror
    </label>
    <label class="block text-sm font-semibold md:col-span-2">English description
        <textarea name="description" rows="3" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $institution->description) }}</textarea>
    </label>
    <label class="block text-sm font-semibold md:col-span-2">Nepali description
        <textarea name="description_ne" rows="3" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description_ne', $institution->description_ne) }}</textarea>
    </label>
    <label class="flex items-center gap-2 text-sm font-semibold">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $institution->is_featured))>
        Featured
    </label>
</div>
<button class="mt-6 rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white">Save</button>
