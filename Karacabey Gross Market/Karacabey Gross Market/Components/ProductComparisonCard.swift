import SwiftUI

struct ProductComparisonCard: View {
    let product: Product
    @EnvironmentObject private var favManager: FavoritesManager
    @EnvironmentObject private var cartManager: CartManager

    var body: some View {
        VStack(alignment: .leading, spacing: 12) {
            // Product Image
            ZStack {
                Color.white
                if let urlStr = product.imageUrl, let url = URL(string: urlStr) {
                    AsyncImage(url: url) { phase in
                        switch phase {
                        case .success(let img):
                            img.resizable().scaledToFit().padding(16)
                        case .empty:
                            ProgressView().tint(.kgmOrange)
                        default:
                            Image(systemName: "photo")
                                .font(.system(size: 32))
                                .foregroundColor(Color(UIColor.systemGray4))
                        }
                    }
                } else {
                    Image(systemName: "photo")
                        .font(.system(size: 32))
                        .foregroundColor(Color(UIColor.systemGray4))
                }
            }
            .frame(height: 120)
            .background(Color(UIColor.secondarySystemBackground))
            .cornerRadius(12)

            // Product Info
            VStack(alignment: .leading, spacing: 8) {
                // Brand
                if let brand = product.brand {
                    Text(brand.uppercased())
                        .font(.system(size: 11, weight: .bold))
                        .foregroundColor(.kgmOrange)
                        .padding(.horizontal, 8)
                        .padding(.vertical, 3)
                        .background(Color.kgmOrange.opacity(0.1))
                        .cornerRadius(4)
                }

                // Name
                Text(product.name)
                    .font(.system(size: 14, weight: .semibold))
                    .lineLimit(2)
                    .foregroundColor(.primary)

                // Price
                HStack(alignment: .bottom, spacing: 8) {
                    Text(product.displayPrice)
                        .font(.system(size: 16, weight: .bold))
                        .foregroundColor(.primary)

                    if product.hasDiscount, let pct = product.discountPercent {
                        Text("%\(pct)")
                            .font(.system(size: 10, weight: .bold))
                            .foregroundColor(.white)
                            .padding(.horizontal, 6)
                            .padding(.vertical, 2)
                            .background(Color.green)
                            .cornerRadius(4)
                    }
                }

                // Stock Status
                HStack(spacing: 4) {
                    Circle()
                        .fill(product.isInStock ? Color.green : Color.red)
                        .frame(width: 6, height: 6)

                    Text(product.isInStock ? "Stokta" : "Tükendi")
                        .font(.system(size: 12, weight: .medium))
                        .foregroundColor(product.isInStock ? .green : .red)
                }
            }

            // Action Buttons
            HStack(spacing: 8) {
                Button(action: {
                    Task {
                        await favManager.toggle(product)
                    }
                }) {
                    Image(systemName: favManager.isFavorite(product.slug) ? "heart.fill" : "heart")
                        .font(.system(size: 16))
                        .foregroundColor(favManager.isFavorite(product.slug) ? .red : .secondary)
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 10)
                        .background(Color(UIColor.secondarySystemBackground))
                        .cornerRadius(8)
                }

                Button(action: {
                    Task {
                        await cartManager.addItem(slug: product.slug, quantity: 1)
                    }
                }) {
                    Image(systemName: "cart.fill")
                        .font(.system(size: 16))
                        .foregroundColor(.white)
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 10)
                        .background(product.isInStock ? Color.kgmOrange : Color.gray)
                        .cornerRadius(8)
                }
                .disabled(!product.isInStock)
            }
        }
        .padding(12)
        .background(Color(UIColor.secondarySystemBackground))
        .cornerRadius(12)
    }
}

#Preview {
    ProductComparisonCard(
        product: Product(
            id: 1,
            name: "Test Ürün",
            slug: "test-urun",
            description: "Test",
            brand: "Brand",
            barcode: nil,
            priceCents: 9999,
            price: "99,99 ₺",
            compareAtPriceCents: nil,
            stockQuantity: 10,
            imageUrl: nil,
            categories: nil
        )
    )
}
