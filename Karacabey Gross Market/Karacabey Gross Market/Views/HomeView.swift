import SwiftUI

struct HomeView: View {
    @StateObject private var viewModel = HomeViewModel()
    
    var body: some View {
        VStack(spacing: 0) {
            CustomAppBar(title: nil, showBackButton: false, showCart: true)
            
            ScrollView {
                VStack(alignment: .leading, spacing: 20) {
                    // Campaign Banner
                    Rectangle()
                        .fill(Color.kgmOrange.opacity(0.15))
                        .frame(height: 180)
                        .cornerRadius(16)
                        .overlay(
                            Text("Toptan Fiyatına\nGüvenle Alışveriş")
                                .font(.poppins(weight: .bold, size: 22))
                                .foregroundColor(.kgmOrange)
                                .multilineTextAlignment(.center)
                        )
                        .padding(.horizontal)
                        .padding(.top, 16)
                    
                    Text("Popüler Ürünler")
                        .font(.poppins(weight: .bold, size: 20))
                        .padding(.horizontal)
                    
                    if viewModel.isLoadingProducts {
                        HStack {
                            Spacer()
                            ProgressView()
                            Spacer()
                        }
                        .padding()
                    } else if let error = viewModel.errorMessage {
                        Text(error)
                            .foregroundColor(.red)
                            .padding()
                    } else {
                        // Horizontal scroll for products
                        ScrollView(.horizontal, showsIndicators: false) {
                            HStack(spacing: 16) {
                                ForEach(viewModel.products) { product in
                                    ProductCard(product: product)
                                }
                            }
                            .padding(.horizontal)
                        }
                    }
                    
                    Spacer()
                }
            }
        }
        .background(Color(UIColor.systemGroupedBackground))
        .navigationBarHidden(true)
        .onAppear {
            Task {
                if viewModel.products.isEmpty {
                    await viewModel.fetchProducts()
                }
            }
        }
    }
}

#Preview {
    HomeView()
}
