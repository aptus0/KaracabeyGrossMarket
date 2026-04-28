import SwiftUI

struct CategoriesView: View {
    @StateObject private var viewModel = CategoriesViewModel()
    
    let columns = [
        GridItem(.flexible()),
        GridItem(.flexible())
    ]
    
    var body: some View {
        VStack(spacing: 0) {
            CustomAppBar(title: "Kategoriler", showBackButton: false, showCart: true)
            
            if viewModel.isLoading {
                Spacer()
                ProgressView("Kategoriler Yükleniyor...")
                Spacer()
            } else if let error = viewModel.errorMessage {
                Spacer()
                Text(error)
                    .foregroundColor(.red)
                Spacer()
            } else {
                ScrollView {
                    LazyVGrid(columns: columns, spacing: 16) {
                        ForEach(viewModel.categories) { category in
                            CategoryCard(category: category)
                        }
                    }
                    .padding()
                }
            }
        }
        .background(Color(UIColor.systemGroupedBackground))
        .navigationBarHidden(true)
        .onAppear {
            Task {
                if viewModel.categories.isEmpty {
                    await viewModel.fetchCategories()
                }
            }
        }
    }
}

struct CategoryCard: View {
    let category: Category
    
    var body: some View {
        VStack {
            if let imageUrlStr = category.imageUrl, let url = URL(string: imageUrlStr) {
                AsyncImage(url: url) { phase in
                    switch phase {
                    case .empty:
                        ProgressView()
                            .frame(height: 100)
                    case .success(let image):
                        image
                            .resizable()
                            .aspectRatio(contentMode: .fit)
                            .frame(height: 100)
                            .padding()
                    case .failure:
                        Image(systemName: "folder")
                            .font(.system(size: 40))
                            .foregroundColor(.gray)
                            .frame(height: 100)
                    @unknown default:
                        EmptyView()
                    }
                }
            } else {
                Image(systemName: "folder")
                    .font(.system(size: 40))
                    .foregroundColor(.gray)
                    .frame(height: 100)
            }
            
            Text(category.name)
                .font(.poppins(weight: .bold, size: 14))
                .foregroundColor(.kgmDarkGray)
                .multilineTextAlignment(.center)
                .padding(.horizontal, 8)
                .padding(.bottom, 12)
        }
        .frame(maxWidth: .infinity)
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: Color.black.opacity(0.05), radius: 5, x: 0, y: 2)
    }
}

#Preview {
    CategoriesView()
}
