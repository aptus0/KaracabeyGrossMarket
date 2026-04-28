import SwiftUI

struct MainTabView: View {
    @State private var selectedTab = 0
    
    var body: some View {
        TabView(selection: $selectedTab) {
            HomeView()
                .tabItem {
                    Image(systemName: "house.fill")
                    Text("Ana Sayfa")
                }
                .tag(0)
            
            CategoriesView()
                .tabItem {
                    Image(systemName: "square.grid.2x2.fill")
                    Text("Kategoriler")
                }
                .tag(1)
            
            Text("Sepet Ekranı")
                .tabItem {
                    Image(systemName: "cart.fill")
                    Text("Sepet")
                }
                .tag(2)
            
            Text("Profil Ekranı")
                .tabItem {
                    Image(systemName: "person.fill")
                    Text("Hesabım")
                }
                .tag(3)
        }
        .accentColor(.kgmOrange)
    }
}

#Preview {
    MainTabView()
}
