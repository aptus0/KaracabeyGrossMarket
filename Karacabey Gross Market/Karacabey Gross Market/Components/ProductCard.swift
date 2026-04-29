import SwiftUI

struct ProductCard: View {
    let product: Product
    @EnvironmentObject private var cartManager: CartManager
    @EnvironmentObject private var favManager: FavoritesManager
    @State private var added = false

    var body: some View {
        VStack(alignment: .leading, spacing: 0) {
            // Image
            ZStack(alignment: .topTrailing) {
                productImage
                    .frame(height: 130)
                    .clipped()

                // Discount badge
                if let pct = product.discountPercent {
                    Text("-%\(pct)")
                        .font(.system(size: 11, weight: .bold))
                        .foregroundColor(.white)
                        .padding(.horizontal, 6).padding(.vertical, 3)
                        .background(Color.green)
                        .cornerRadius(8)
                        .padding(6)
                }
            }
            .cornerRadius(12)

            VStack(alignment: .leading, spacing: 4) {
                if let brand = product.brand {
                    Text(brand)
                        .font(.poppins(weight: .medium, size: 10))
                        .foregroundColor(.kgmGray)
                        .lineLimit(1)
                }

                Text(product.name)
                    .font(.poppins(weight: .medium, size: 13))
                    .foregroundColor(.kgmDarkGray)
                    .lineLimit(2)
                    .frame(minHeight: 36, alignment: .topLeading)

                Text(product.displayPrice)
                    .font(.poppins(weight: .bold, size: 15))
                    .foregroundColor(.kgmOrange)

                // Add to cart
                Button {
                    Task {
                        await cartManager.addItem(slug: product.slug)
                        added = true
                        DispatchQueue.main.asyncAfter(deadline: .now() + 1.2) { added = false }
                    }
                } label: {
                    HStack(spacing: 4) {
                        Image(systemName: added ? "checkmark" : "plus")
                            .font(.system(size: 11, weight: .bold))
                        Text(added ? "Eklendi" : "Sepete Ekle")
                            .font(.poppins(weight: .bold, size: 12))
                    }
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 7)
                    .background(added ? Color.green : Color.kgmOrange)
                    .foregroundColor(.white)
                    .cornerRadius(8)
                    .animation(.easeInOut(duration: 0.2), value: added)
                }
                .disabled(!product.isInStock)
            }
            .padding(10)
        }
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.06), radius: 5, x: 0, y: 2)
    }

    private var productImage: some View {
        ZStack {
            Color(UIColor.secondarySystemBackground)
            if let urlStr = product.imageUrl, let url = URL(string: urlStr) {
                AsyncImage(url: url) { phase in
                    switch phase {
                    case .success(let img):
                        img.resizable().scaledToFit().padding(8)
                    default:
                        Image(systemName: "photo").foregroundColor(.gray.opacity(0.5))
                    }
                }
            } else {
                Image(systemName: "photo").foregroundColor(.gray.opacity(0.5))
            }
        }
    }
}
