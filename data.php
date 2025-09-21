<?php
/* ====== DATA (replace with DB later) ====== */
$categories = [
  ["id" => "all", "name" => "All Categories"],
  ["id" => "gaming-pcs", "name" => "Gaming PCs"],
  ["id" => "graphics-cards", "name" => "Graphics Cards"],
  ["id" => "motherboards", "name" => "Motherboards"],
  ["id" => "monitors", "name" => "Monitors"],
  ["id" => "peripherals", "name" => "Peripherals"],
  ["id" => "audio", "name" => "Audio"]
];

$products = [
  [
    "id" => "1",
    "name" => "TechGiant Gaming PC Elite",
    "category" => "gaming-pcs",
    "price" => 44999.99,
    "old_price" => 49999.99,
    "discount" => "-10%",
    "stars" => 4.5,
    "reviews" => 247,
    "image" => "https://images.unsplash.com/photo-1636914011676-039d36b73765?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBQQyUyMGRlc2t0b3AlMjBjb21wdXRlcnxlbnwxfHx8fDE3NTcwMTc2MTN8MA&ixlib=rb-4.1.0&q=80&w=1080",
    "in_stock" => true
  ],
  [
    "id" => "2",
    "name" => "RTX 4080 Super Graphics Card",
    "category" => "graphics-cards",
    "price" => 21999.99,
    "old_price" => null,
    "discount" => null,
    "stars" => 4.6,
    "reviews" => 189,
    "image" => "https://images.unsplash.com/photo-1634672350437-f9632adc9c3f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBncmFwaGljcyUyMGNhcmQlMjBHUFV8ZW58MXx8fHwxNzU2OTk1Mjg4fDA&ixlib=rb-4.1.0&q=80&w=1080",
    "in_stock" => true
  ],
  [
    "id" => "3",
    "name" => "Gaming Motherboard Z790",
    "category" => "motherboards",
    "price" => 6499.99,
    "old_price" => null,
    "discount" => null,
    "stars" => 4.3,
    "reviews" => 156,
    "image" => "https://images.unsplash.com/photo-1694444070793-13db645409f4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBtb3RoZXJib2FyZCUyMGNvbXB1dGVyJTIwcGFydHN8ZW58MXx8fHwxNzU3MDE3NjE0fDA&ixlib=rb-4.1.0&q=80&w=1080",
    "in_stock" => true
  ],
  [
    "id" => "4",
    "name" => "4K Gaming Monitor 27\"",
    "category" => "monitors",
    "price" => 10999.99,
    "old_price" => 12999.99,
    "discount" => "-15%",
    "stars" => 4.4,
    "reviews" => 203,
    "image" => "https://images.unsplash.com/photo-1696710240292-05aad88b94b8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBtb25pdG9yJTIwc2V0dXB8ZW58MXx8fHwxNzU3MDE3NjE0fDA&ixlib=rb-4.1.0&q=80&w=1080",
    "in_stock" => true
  ],
  [
    "id" => "5",
    "name" => "RGB Gaming Keyboard & Mouse",
    "category" => "peripherals",
    "price" => 2799.99,
    "old_price" => null,
    "discount" => null,
    "stars" => 4.0,
    "reviews" => 321,
    "image" => "https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080",
    "in_stock" => true
  ],
  [
    "id" => "6",
    "name" => "Gaming Headset Pro",
    "category" => "audio",
    "price" => 1899.99,
    "old_price" => null,
    "discount" => null,
    "stars" => 4.1,
    "reviews" => 178,
    "image" => "https://images.unsplash.com/photo-1616081118924-20e97edd3b3d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBoZWFkc2V0JTIwYXVkaW98ZW58MXx8fHwxNzU3MDE3NjE1fDA&ixlib=rb-4.1.0&q=80&w=1080",
    "in_stock" => false
  ],
];
?>


