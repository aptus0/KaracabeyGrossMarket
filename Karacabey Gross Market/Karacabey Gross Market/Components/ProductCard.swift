import SwiftUI

struct ProductCard: View {
    let product: Product
    
    var body: some View {
        VStack(alignment: .leading) {
            // Product Image
            if let imageUrlStr = product.imageUrl, let url = URL(string: imageUrlStr) {
                AsyncImage(url: url) { phase in
                    switch phase {
                    case .empty:
                        ProgressView()
                            .frame(width: 140, height: 140)
                    case .success(let image):
                        image
                            .resizable()
                            .aspectRatio(contentMode: .fill)
                            .frame(width: 140, height: 140)
                            .clipped()
                            .cornerRadius(12)
                    case .failure:
                        ImagePlaceholder()
                    @unknown default:
                        ImagePlaceholder()
                    }
                }
            } else {
                ImagePlaceholder()
            }
            
            Text(product.name)
                .font(.poppins(weight: .medium, size: 14))
                .lineLimit(2)
                .frame(height: 40, alignment: .topLeading)
            
            Text(product.displayPrice)
                .font(.poppins(weight: .bold, size: 16))
                .foregroundColor(.kgmOrange)
            
            Button(action: {
                // Add to cart action
            }) {
                Text("Sepete Ekle")
                    .font(.poppins(weight: .bold, size: 12))
                    .foregroundColor(.kgmWhite)
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 8)
                    .background(Color.kgmOrange)
                    .cornerRadius(8)
            }
        }
        .frame(width: 140)
        .padding(12)
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: Color.black.opacity(0.05), radius: 5, x: 0, y: 2)
    }
}

struct ImagePlaceholder: View {
    var body: some View {
        Rectangle()
            .fill(Color.gray.opacity(0.2))
            .frame(width: 140, height: 140)
            .cornerRadius(12)
            .overlay(
                Image(systemName: "photo")
                    .foregroundColor(.gray)
            )
    }
}
