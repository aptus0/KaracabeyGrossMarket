import SwiftUI

struct LoginView: View {
    @StateObject private var viewModel = AuthViewModel()
    @EnvironmentObject private var authManager: AuthManager
    @State private var phone = ""
    @State private var password = ""
    @State private var showRegister = false
    @Environment(\.dismiss) private var dismiss

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 28) {
                    // Logo / header
                    VStack(spacing: 8) {
                        Image("AppLogo")
                            .resizable()
                            .scaledToFit()
                            .frame(width: 80, height: 80)
                            .padding(16)
                            .background(Circle().fill(Color.white).shadow(radius: 5))
                        Text("Karacabey Gross Market")
                            .font(.poppins(weight: .bold, size: 22))
                        Text("Hesabınıza giriş yapın")
                            .font(.poppins(weight: .regular, size: 14))
                            .foregroundColor(.kgmGray)
                    }
                    .padding(.top, 40)

                    // Form
                    VStack(spacing: 16) {
                        KGMTextField(label: "Telefon Numarası", placeholder: "5XX XXX XX XX", text: $phone,
                                     keyboardType: .phonePad)
                        KGMSecureField(label: "Şifre", placeholder: "En az 8 karakter", text: $password)
                    }
                    .padding(.horizontal)

                    if let err = viewModel.errorMessage {
                        Text(err)
                            .font(.poppins(weight: .regular, size: 13))
                            .foregroundColor(.red)
                            .multilineTextAlignment(.center)
                            .padding(.horizontal)
                    }

                    // Submit
                    Button {
                        Task {
                            if await viewModel.login(phone: phone, password: password) {
                                dismiss()
                            }
                        }
                    } label: {
                        Group {
                            if viewModel.isLoading {
                                ProgressView().tint(.white)
                            } else {
                                Text("Giriş Yap").font(.poppins(weight: .bold, size: 16))
                            }
                        }
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 14)
                        .background(Color.kgmOrange)
                        .foregroundColor(.white)
                        .cornerRadius(14)
                    }
                    .disabled(viewModel.isLoading || phone.isEmpty || password.isEmpty)
                    .padding(.horizontal)

                    // Register link
                    Button {
                        showRegister = true
                    } label: {
                        Text("Hesabınız yok mu? ")
                            .foregroundColor(.kgmGray) +
                        Text("Kayıt Olun")
                            .foregroundColor(.kgmOrange)
                    }
                    .font(.poppins(weight: .medium, size: 14))

                    Spacer()
                }
            }
            .navigationDestination(isPresented: $showRegister) { RegisterView() }
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .topBarLeading) {
                    Button { dismiss() } label: {
                        Image(systemName: "xmark").foregroundColor(.kgmDarkGray)
                    }
                }
            }
        }
    }
}

// MARK: - Reusable Fields
struct KGMTextField: View {
    let label: String
    let placeholder: String
    @Binding var text: String
    var keyboardType: UIKeyboardType = .default

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text(label).font(.poppins(weight: .medium, size: 13)).foregroundColor(.kgmDarkGray)
            TextField(placeholder, text: $text)
                .keyboardType(keyboardType)
                .autocorrectionDisabled()
                .textInputAutocapitalization(.never)
                .padding(12)
                .background(Color(UIColor.secondarySystemBackground))
                .cornerRadius(10)
        }
    }
}

struct KGMSecureField: View {
    let label: String
    let placeholder: String
    @Binding var text: String
    @State private var show = false

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text(label).font(.poppins(weight: .medium, size: 13)).foregroundColor(.kgmDarkGray)
            HStack {
                Group {
                    if show { TextField(placeholder, text: $text) }
                    else    { SecureField(placeholder, text: $text) }
                }
                .autocorrectionDisabled()
                .textInputAutocapitalization(.never)
                Button { show.toggle() } label: {
                    Image(systemName: show ? "eye.slash" : "eye").foregroundColor(.kgmGray)
                }
            }
            .padding(12)
            .background(Color(UIColor.secondarySystemBackground))
            .cornerRadius(10)
        }
    }
}
