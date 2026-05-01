import SwiftUI

struct CheckoutView: View {
    @EnvironmentObject private var authManager: AuthManager
    @EnvironmentObject private var cartManager: CartManager
    @StateObject private var viewModel = CheckoutViewModel()
    @State private var showPaymentWebView = false
    @Environment(\.dismiss) var dismiss

    var body: some View {
        NavigationStack {
            Form {
                Section("Kişisel Bilgiler") {
                    TextField("Ad Soyad", text: $viewModel.customerName)
                        .textContentType(.name)

                    TextField("E-posta", text: $viewModel.customerEmail)
                        .textContentType(.emailAddress)
                        .keyboardType(.emailAddress)

                    TextField("Telefon", text: $viewModel.customerPhone)
                        .textContentType(.telephoneNumber)
                        .keyboardType(.phonePad)
                }

                Section("Teslimat Adresi") {
                    TextField("Şehir", text: $viewModel.shippingCity)

                    TextField("İlçe", text: $viewModel.shippingDistrict)

                    TextField("Adres", text: $viewModel.shippingAddress)
                        .lineLimit(3, reservesSpace: true)
                }

                Section("Sipariş Özeti") {
                    if let cart = cartManager.cart {
                        summaryRow("Ara Toplam", value: cart.formattedSubtotal)
                        if cart.discountCents > 0 {
                            summaryRow("İndirim", value: "-\(cart.formattedDiscount)", color: .green)
                        }
                        Divider()
                        summaryRow("Toplam", value: cart.formattedTotal, bold: true)
                    }
                }

                if let err = viewModel.errorMessage {
                    Section {
                        Text(err)
                            .font(.poppins(weight: .regular, size: 13))
                            .foregroundColor(.red)
                    }
                }

                Section {
                    Button(action: {
                        Task {
                            if await viewModel.initiateCheckout() {
                                showPaymentWebView = true
                            }
                        }
                    }) {
                        if viewModel.isLoading {
                            ProgressView()
                                .frame(maxWidth: .infinity, alignment: .center)
                        } else {
                            Text("Ödemeye Geç")
                                .frame(maxWidth: .infinity)
                                .foregroundColor(.white)
                        }
                    }
                    .listRowBackground(Color.kgmOrange)
                    .disabled(viewModel.isLoading)
                }
            }
            .navigationTitle("Sipariş Tamamla")
            .navigationBarTitleDisplayMode(.inline)
            .navigationDestination(isPresented: $showPaymentWebView) {
                if let response = viewModel.checkoutResponse {
                    PaymentWebView(token: response.token, onDismiss: {
                        showPaymentWebView = false
                    })
                }
            }
        }
    }

    private func summaryRow(_ label: String, value: String, color: Color = .kgmDarkGray, bold: Bool = false) -> some View {
        HStack {
            Text(label).font(.poppins(weight: bold ? .bold : .regular, size: 14))
            Spacer()
            Text(value).font(.poppins(weight: bold ? .bold : .medium, size: 14)).foregroundColor(color)
        }
    }
}
