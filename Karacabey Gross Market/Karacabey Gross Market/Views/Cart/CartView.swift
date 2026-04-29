import SwiftUI

struct CartView: View {
    @EnvironmentObject private var cartManager: CartManager
    @EnvironmentObject private var authManager: AuthManager
    @State private var couponText = ""
    @State private var couponError: String?
    @State private var couponSuccess = false

    var body: some View {
        Group {
            if cartManager.isLoading && cartManager.cart == nil {
                ProgressView("Sepet yükleniyor…").frame(maxWidth: .infinity, maxHeight: .infinity)
            } else if cartManager.isEmpty {
                emptyCart
            } else {
                cartContent
            }
        }
        .navigationTitle("Sepetim")
        .navigationBarTitleDisplayMode(.inline)
        .task {
            if authManager.isLoggedIn { await cartManager.fetchCart() }
        }
    }

    private var emptyCart: some View {
        VStack(spacing: 20) {
            Spacer()
            Image(systemName: "cart").font(.system(size: 72)).foregroundColor(.gray.opacity(0.4))
            Text("Sepetiniz boş").font(.poppins(weight: .bold, size: 20)).foregroundColor(.secondary)
            Text("Ürün eklemek için alışverişe başlayın")
                .font(.poppins(weight: .regular, size: 14)).foregroundColor(.secondary)
            NavigationLink(destination: ProductsView()) {
                Text("Alışverişe Başla")
                    .font(.poppins(weight: .bold, size: 16))
                    .frame(maxWidth: 240)
                    .padding(.vertical, 14)
                    .background(Color.kgmOrange)
                    .foregroundColor(.white)
                    .cornerRadius(14)
            }
            Spacer()
        }
    }

    private var cartContent: some View {
        VStack(spacing: 0) {
            ScrollView {
                VStack(spacing: 12) {
                    ForEach(cartManager.cart?.items ?? []) { item in
                        CartItemRow(item: item)
                    }
                    couponSection
                }
                .padding()
            }
            orderSummary
        }
    }

    private var couponSection: some View {
        VStack(alignment: .leading, spacing: 8) {
            Text("Kupon Kodu").font(.poppins(weight: .bold, size: 15))
            HStack {
                TextField("KGM25", text: $couponText)
                    .textInputAutocapitalization(.characters)
                    .padding(10)
                    .background(Color(UIColor.secondarySystemBackground))
                    .cornerRadius(10)
                Button("Uygula") {
                    Task {
                        couponError = nil
                        do {
                            try await cartManager.applyCoupon(couponText.uppercased())
                            couponSuccess = true
                            couponText    = ""
                        } catch {
                            couponError = (error as? NetworkError)?.errorDescription ?? "Geçersiz kupon."
                        }
                    }
                }
                .font(.poppins(weight: .bold, size: 14))
                .padding(.horizontal, 14).padding(.vertical, 10)
                .background(Color.kgmOrange)
                .foregroundColor(.white)
                .cornerRadius(10)
            }
            if let err = couponError { Text(err).font(.caption).foregroundColor(.red) }
            if couponSuccess { Text("Kupon uygulandı ✓").font(.caption).foregroundColor(.green) }
        }
        .padding()
        .background(Color.white)
        .cornerRadius(16)
    }

    private var orderSummary: some View {
        VStack(spacing: 0) {
            Divider()
            VStack(spacing: 10) {
                if let cart = cartManager.cart {
                    summaryRow("Ara Toplam", value: cart.formattedSubtotal)
                    if cart.discountCents > 0 {
                        summaryRow("İndirim", value: "-\(cart.formattedDiscount)", color: .green)
                    }
                    Divider()
                    summaryRow("Toplam", value: cart.formattedTotal, bold: true)
                }
                NavigationLink(destination: CheckoutView()) {
                    Text("Siparişi Tamamla")
                        .font(.poppins(weight: .bold, size: 16))
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 14)
                        .background(Color.kgmOrange)
                        .foregroundColor(.white)
                        .cornerRadius(14)
                }
            }
            .padding()
        }
        .background(Color.white)
        .shadow(color: .black.opacity(0.08), radius: 8, x: 0, y: -2)
    }

    private func summaryRow(_ label: String, value: String, color: Color = .kgmDarkGray, bold: Bool = false) -> some View {
        HStack {
            Text(label).font(.poppins(weight: bold ? .bold : .regular, size: 14))
            Spacer()
            Text(value).font(.poppins(weight: bold ? .bold : .medium, size: 14)).foregroundColor(color)
        }
    }
}

// MARK: - Cart Item Row
struct CartItemRow: View {
    let item: CartItem
    @EnvironmentObject private var cartManager: CartManager

    var body: some View {
        HStack(spacing: 12) {
            // Image
            RoundedRectangle(cornerRadius: 10)
                .fill(Color(UIColor.secondarySystemBackground))
                .frame(width: 72, height: 72)
                .overlay(
                    Group {
                        if let url = item.product.imageUrl.flatMap(URL.init) {
                            AsyncImage(url: url) { phase in
                                if case .success(let img) = phase { img.resizable().scaledToFit().padding(6) }
                                else { Image(systemName: "photo").foregroundColor(.gray) }
                            }
                        } else {
                            Image(systemName: "photo").foregroundColor(.gray)
                        }
                    }
                )

            // Info
            VStack(alignment: .leading, spacing: 4) {
                Text(item.product.name)
                    .font(.poppins(weight: .medium, size: 13))
                    .lineLimit(2)
                Text(item.formattedLineTotal)
                    .font(.poppins(weight: .bold, size: 15))
                    .foregroundColor(.kgmOrange)
            }

            Spacer()

            // Stepper
            VStack(spacing: 8) {
                Button {
                    Task { await cartManager.updateItem(id: item.id, quantity: item.quantity + 1) }
                } label: {
                    Image(systemName: "plus.circle.fill")
                        .font(.system(size: 24)).foregroundColor(.kgmOrange)
                }
                Text("\(item.quantity)").font(.poppins(weight: .bold, size: 14))
                Button {
                    Task { await cartManager.updateItem(id: item.id, quantity: item.quantity - 1) }
                } label: {
                    Image(systemName: item.quantity > 1 ? "minus.circle.fill" : "trash.circle.fill")
                        .font(.system(size: 24)).foregroundColor(item.quantity > 1 ? .kgmOrange : .red)
                }
            }
        }
        .padding(12)
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.05), radius: 4, x: 0, y: 2)
    }
}
