import SwiftUI

struct RegisterView: View {
    @StateObject private var viewModel = AuthViewModel()
    @State private var name = ""
    @State private var email = ""
    @State private var password = ""
    @Environment(\.dismiss) private var dismiss

    var body: some View {
        ScrollView {
            VStack(spacing: 28) {
                VStack(spacing: 8) {
                    Image(systemName: "person.badge.plus")
                        .font(.system(size: 56)).foregroundColor(.kgmOrange)
                    Text("Hesap Oluştur")
                        .font(.poppins(weight: .bold, size: 22))
                    Text("Ücretsiz kayıt olun")
                        .font(.poppins(weight: .regular, size: 14)).foregroundColor(.kgmGray)
                }
                .padding(.top, 20)

                VStack(spacing: 16) {
                    KGMTextField(label: "Ad Soyad", placeholder: "Adınız Soyadınız", text: $name)
                    KGMTextField(label: "E-posta", placeholder: "ornek@mail.com", text: $email, keyboardType: .emailAddress)
                    KGMSecureField(label: "Şifre", placeholder: "En az 8 karakter", text: $password)
                }
                .padding(.horizontal)

                if let err = viewModel.errorMessage {
                    Text(err).font(.caption).foregroundColor(.red)
                        .multilineTextAlignment(.center).padding(.horizontal)
                }

                Button {
                    Task {
                        if await viewModel.register(name: name, email: email, password: password) {
                            dismiss()
                        }
                    }
                } label: {
                    Group {
                        if viewModel.isLoading { ProgressView().tint(.white) }
                        else { Text("Kayıt Ol").font(.poppins(weight: .bold, size: 16)) }
                    }
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 14)
                    .background(Color.kgmOrange)
                    .foregroundColor(.white)
                    .cornerRadius(14)
                }
                .disabled(viewModel.isLoading || name.isEmpty || email.isEmpty || password.count < 6)
                .padding(.horizontal)

                Button { dismiss() } label: {
                    Text("Zaten hesabınız var mı? ")
                        .foregroundColor(.kgmGray) +
                    Text("Giriş Yapın").foregroundColor(.kgmOrange)
                }
                .font(.poppins(weight: .medium, size: 14))
            }
        }
        .navigationTitle("Kayıt Ol")
        .navigationBarTitleDisplayMode(.inline)
    }
}
