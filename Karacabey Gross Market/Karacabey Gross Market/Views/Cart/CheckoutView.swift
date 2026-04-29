import SwiftUI
import WebKit

struct CheckoutView: View {
    @EnvironmentObject private var authManager: AuthManager
    // Web checkout URL – aynı backend'in storefront checkout sayfası
    private var checkoutURL: URL {
        let base = APIClient.shared.baseURL
            .replacingOccurrences(of: "/api/v1", with: "")
        return URL(string: "\(base)/checkout") ?? URL(string: "http://karacabey-gross-market.test/checkout")!
    }

    var body: some View {
        WebView(url: checkoutURL)
            .navigationTitle("Sipariş Özeti")
            .navigationBarTitleDisplayMode(.inline)
            .ignoresSafeArea(edges: .bottom)
    }
}

// MARK: - WKWebView wrapper
struct WebView: UIViewRepresentable {
    let url: URL

    func makeUIView(context: Context) -> WKWebView {
        let wv = WKWebView()
        wv.load(URLRequest(url: url))
        return wv
    }

    func updateUIView(_ uiView: WKWebView, context: Context) {}
}
