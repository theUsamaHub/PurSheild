<?php
$file = 'f:\Techwiz\PurSheild\resources\views\public\vets.blade.php';
$content = file_get_contents($file);

$dynamicContent = <<<BLADE
        <div class="directory-grid">
            @forelse(\$vets as \$vet)
            <article class="profile-card vip-tilt reveal-up"
                     data-category="general"
                     data-name="{{ strtolower(\$vet->name) }}">
                <div class="profile-photo">
                    <img src="{{ \$vet->vetProfile?->profile_image ?? asset('images/vet.jpg') }}" alt="Veterinarian">
                    <span class="availability-dot"></span>
                </div>
                <div class="profile-body">
                    <div class="profile-top">
                        <div>
                            <span>Veterinarian</span>
                            <h3>Dr. {{ \$vet->name }}</h3>
                        </div>
                        <div class="profile-rating">
                            <i class="fa-solid fa-star"></i> 4.9
                        </div>
                    </div>
                    <p>{{ Str::limit(\$vet->vetProfile?->bio ?? 'General veterinary consultations, preventive care and routine pet wellness.', 100) }}</p>
                    <div class="profile-meta">
                        <span><i class="fa-solid fa-briefcase-medical"></i> {{ \$vet->vetProfile?->experience_years ?? 0 }} Years</span>
                        <span><i class="fa-solid fa-location-dot"></i> {{ \$vet->vetProfile?->city ?? 'Unknown' }}</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn-outline">View Profile</button>
                    <button class="btn-primary">Book Visit</button>
                </div>
            </article>
            @empty
            <p>No veterinarians found.</p>
            @endforelse
        </div>
        
        <div style="margin-top:20px;">
            {{ \$vets->links() }}
        </div>
BLADE;

$content = preg_replace('/<div class="directory-grid">.*?<\/div>\s*<\/section>/s', $dynamicContent . "\n\n    </section>", $content);
file_put_contents($file, $content);

$file2 = 'f:\Techwiz\PurSheild\resources\views\public\shelters.blade.php';
$content2 = file_get_contents($file2);
$dynamicContent2 = <<<BLADE
        <div class="directory-grid">
            @forelse(\$shelters as \$shelter)
            <article class="profile-card vip-tilt reveal-up" data-category="general">
                <div class="profile-photo">
                    <img src="{{ \$shelter->shelterProfile?->logo ?? asset('images/shelter.jpg') }}" alt="Shelter">
                </div>
                <div class="profile-body">
                    <div class="profile-top">
                        <div>
                            <span>Animal Shelter</span>
                            <h3>{{ \$shelter->name }}</h3>
                        </div>
                    </div>
                    <p>{{ Str::limit(\$shelter->shelterProfile?->description ?? 'Providing care, love, and shelter to animals in need.', 100) }}</p>
                    <div class="profile-meta">
                        <span><i class="fa-solid fa-location-dot"></i> {{ \$shelter->shelterProfile?->city ?? 'Unknown' }}</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn-outline">View Shelter</button>
                    <button class="btn-primary">Adopt</button>
                </div>
            </article>
            @empty
            <p>No shelters found.</p>
            @endforelse
        </div>
        
        <div style="margin-top:20px;">
            {{ \$shelters->links() }}
        </div>
BLADE;
$content2 = preg_replace('/<div class="directory-grid">.*?<\/div>\s*<\/section>/s', $dynamicContent2 . "\n\n    </section>", $content2);
file_put_contents($file2, $content2);

$file3 = 'f:\Techwiz\PurSheild\resources\views\public\products.blade.php';
$content3 = file_get_contents($file3);
$dynamicContent3 = <<<BLADE
        <div class="shop-grid">
            @forelse(\$products as \$product)
            <article class="shop-card vip-tilt reveal-up">
                <div class="shop-image">
                    <img src="{{ \$product->image_url ?? asset('images/product.jpg') }}" alt="{{ \$product->name }}">
                </div>
                <div class="shop-body">
                    <div class="shop-top">
                        <h3>{{ \$product->name }}</h3>
                    </div>
                    <div class="shop-price">Rs. {{ number_format(\$product->price, 2) }}</div>
                </div>
                <div class="shop-actions">
                    <button class="btn-outline">View Details</button>
                    <button class="btn-primary">Add to Cart</button>
                </div>
            </article>
            @empty
            <p>No products available.</p>
            @endforelse
        </div>
        
        <div style="margin-top:20px;">
            {{ \$products->links() }}
        </div>
BLADE;
$content3 = preg_replace('/<div class="shop-grid">.*?<\/div>\s*<\/section>/s', $dynamicContent3 . "\n\n    </section>", $content3);
file_put_contents($file3, $content3);

echo "Updated vets, shelters, and products.";
