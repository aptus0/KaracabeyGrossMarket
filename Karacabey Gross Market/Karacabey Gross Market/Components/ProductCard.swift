import SwiftUI

struct ProductCard: View {
    let product: Product
    @EnvironmentObject private var cartManager: CartManager
    @EnvironmentObject private var favManager: FavoritesManager
    @State private var added = false

    var body: some View {
        VStack(alignment: .leading, spacing: 0) {

            // Image area
            ZStack(alignment: .topTrailing) {
                productImage
                    .frame(height: 136)
                    .clipped()

                if let pct = product.discountPercent {
                    Text("-%\(pct)")
                        .font(.system(size: 10, weight: .black))
                        .foregroundColor(.white)
                        .padding(.horizontal, 7).padding(.vertical, 4)
                        .background(Color.red)
                        .cornerRadius(8)
                        .padding(8)
                }
            }
            .cornerRadius(14)

            // Info
            VStack(alignment: .leading, spacing: 5) {
                if let brand = product.brand {
                    Text(brand.uppercased())
                        .font(.poppins(weight: .semibold, size: 9))
                        .foregroundColor(.kgmOrange)
                        .lineLimit(1)
                }

                Text(product.name)
                    .font(.poppins(weight: .medium, size: 12))
                    .foregroundColor(.kgmDarkGray)
                    .lineLimit(2)
                    .frame(minHeight: 34, alignment: .topLeading)

                HStack(alignment: .firstTextBaseline, spacing: 4) {
                    Text(product.displayPrice)
                        .font(.poppins(weight: .bold, size: 16))
                        .foregroundColor(.kgmOrange)
                    if let orig = product.originalPrice {
                        Text(orig)
                            .font(.poppins(weight: .regular, size: 11))
                            .foregroundColor(.kgmGray)
                            .strikethrough()
                    }
                }

                Button {
                    guard !added else { return }
                    Task {
                        await cartManager.addItem(slug: product.slug)
                        withAnimation(.spring(response: 0.3)) { added = true }
                        DispatchQueue.main.asyncAfter(deadline: .now() + 1.4) {
                            withAnimation { added = false }
                        }
                    }
                } label: {
                    HStack(spacing: 5) {
                        Image(systemName: added ? "checkmark" : "cart.badge.plus")
                            .font(.system(size: 11, weight: .bold))
                        Text(added ? "Eklendi" : "Sepete Ekle")
                            .font(.poppins(weight: .bold, size: 11))
                    }
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 8)
                    .background(added ? Color.green : Color.kgmOrange)
                    .foregroundColor(.white)
                    .cornerRadius(10)
                }
                .disabled(!product.isInStock || added)
                .opacity(product.isInStock ? 1 : 0.5)
            }
            .padding(10)
        }
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.07), radius: 8, x: 0, y: 3)
    }

    private var productImage: some View {
        ZStack {
            Color(.secondarySystemBackground)
            if let urlStr = product.imageUrl, let url = URL(string: urlStr) {
                AsyncImage(url: url) { phase in
                    switch phase {
                    case .success(let img):
                        img.resizable().scaledToFit().padding(8)
                    default:
                        placeholderIcon
                    }
                }
            } else {
                placeholderIcon
            }
        }
    }

    private var placeholderIcon: some View {
        Image(systemName: "photo")
            .font(.system(size: 28))
            .foregroundColor(.gray.opacity(0.3))
    }
}
