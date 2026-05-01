import SwiftUI

struct FilterSheetView: View {
    @ObservedObject var viewModel: ProductsViewModel
    @Binding var isPresented: Bool
    @State private var tempFilter = ProductFilter()

    var body: some View {
        NavigationStack {
            Form {
                Section("Sıralama") {
                    Picker("Sıralama", selection: $tempFilter.sortBy) {
                        ForEach(ProductFilter.SortOption.allCases, id: \.self) { option in
                            Text(option.displayName).tag(option)
                        }
                    }
                }

                Section("Fiyat Aralığı") {
                    HStack {
                        Text("Min: ₺\(tempFilter.priceMin ?? 0)")
                        Spacer()
                        Text("Max: ₺\(tempFilter.priceMax ?? 10000)")
                    }

                    Slider(
                        value: Binding(
                            get: { Double(tempFilter.priceMin ?? 0) },
                            set: { tempFilter.priceMin = Int($0) }
                        ),
                        in: 0...10000,
                        step: 100
                    )

                    Slider(
                        value: Binding(
                            get: { Double(tempFilter.priceMax ?? 10000) },
                            set: { tempFilter.priceMax = Int($0) }
                        ),
                        in: 0...10000,
                        step: 100
                    )
                }

                Section("Stok") {
                    Toggle("Sadece Stokta Olanlar", isOn: $tempFilter.inStockOnly)
                }

                Section("İndirim") {
                    Toggle("Sadece İndirimli Ürünler", isOn: $tempFilter.discountOnly)
                }

                Section("Minimum Puan") {
                    Picker("Puan", selection: $tempFilter.ratings) {
                        Text("Hepsi").tag(Optional<Int>(nil))
                        Text("3★+").tag(Optional<Int>(3))
                        Text("4★+").tag(Optional<Int>(4))
                        Text("5★").tag(Optional<Int>(5))
                    }
                }

                Section {
                    Button("Filtreleri Uygula") {
                        Task {
                            await viewModel.load(category: nil)
                            isPresented = false
                        }
                    }
                    .frame(maxWidth: .infinity)
                    .foregroundColor(.white)
                    .listRowBackground(Color.kgmOrange)

                    Button("Sıfırla") {
                        tempFilter = ProductFilter()
                    }
                    .frame(maxWidth: .infinity)
                    .foregroundColor(.red)
                }
            }
            .navigationTitle("Filtreler")
            .navigationBarTitleDisplayMode(.inline)
        }
    }
}
