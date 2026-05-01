import SwiftUI

struct CardInputView: View {
    @State private var cardModel = CardInputModel()
    @State private var savedCards: [PaymentCard] = []
    @State private var selectedCardId: String?
    @State private var showNewCard = false
    @State private var isProcessing = false
    let onPayment: (CardInputModel?, String?) async -> Void

    var body: some View {
        VStack(spacing: 20) {
            // Saved Cards
            if !savedCards.isEmpty {
                VStack(alignment: .leading, spacing: 12) {
                    Text("Kayıtlı Kartlar")
                        .font(.poppins(weight: .bold, size: 16))
                        .foregroundColor(.primary)

                    ScrollView(.horizontal, showsIndicators: false) {
                        HStack(spacing: 12) {
                            ForEach(savedCards) { card in
                                CardDisplayView(card: card, isSelected: selectedCardId == card.id) {
                                    selectedCardId = card.id
                                    showNewCard = false
                                }
                            }

                            Button(action: { showNewCard = true; selectedCardId = nil }) {
                                VStack(spacing: 8) {
                                    Image(systemName: "plus")
                                        .font(.system(size: 24, weight: .semibold))
                                    Text("Yeni Kart")
                                        .font(.poppins(weight: .semibold, size: 12))
                                }
                                .frame(maxWidth: .infinity)
                                .frame(height: 160)
                                .background(Color(.secondarySystemBackground))
                                .foregroundColor(.kgmOrange)
                                .cornerRadius(12)
                            }
                        }
                    }
                }
                .padding()
                .background(Color(.systemBackground))
                .cornerRadius(12)
            }

            // New Card Input
            if showNewCard {
                NewCardInputSection(cardModel: $cardModel)
                    .padding()
                    .background(Color(.secondarySystemBackground))
                    .cornerRadius(12)
            }

            // Payment Button
            Button(action: {
                Task {
                    isProcessing = true
                    if showNewCard {
                        await onPayment(cardModel, nil)
                    } else if let cardId = selectedCardId {
                        await onPayment(nil, cardId)
                    }
                    isProcessing = false
                }
            }) {
                if isProcessing {
                    ProgressView()
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 14)
                } else {
                    Text("3D Secure Ödeme Yap")
                        .font(.poppins(weight: .bold, size: 16))
                        .frame(maxWidth: .infinity)
                        .padding(.vertical, 14)
                        .background(Color.kgmOrange)
                        .foregroundColor(.white)
                        .cornerRadius(12)
                }
            }
            .disabled(isProcessing || (!showNewCard && selectedCardId == nil) || (showNewCard && !cardModel.isValid))

            Spacer()
        }
        .padding()
        .navigationTitle("Ödeme Yöntemi")
        .navigationBarTitleDisplayMode(.inline)
        .task {
            await loadSavedCards()
        }
    }

    private func loadSavedCards() async {
        // Load from API or local storage
        // savedCards = await fetchSavedCards()
    }
}

// MARK: - Card Display
struct CardDisplayView: View {
    let card: PaymentCard
    let isSelected: Bool
    let onTap: () -> Void

    var body: some View {
        Button(action: onTap) {
            VStack(alignment: .leading, spacing: 12) {
                HStack {
                    Image(systemName: card.brand.icon)
                        .font(.system(size: 24))
                        .foregroundColor(.kgmOrange)

                    Spacer()

                    if isSelected {
                        Image(systemName: "checkmark.circle.fill")
                            .font(.system(size: 20))
                            .foregroundColor(.green)
                    }
                }

                Text(card.cardNumber)
                    .font(.poppins(weight: .semibold, size: 14))
                    .foregroundColor(.primary)

                HStack {
                    Text(card.cardholderName)
                        .font(.poppins(weight: .regular, size: 12))
                        .foregroundColor(.secondary)
                    Spacer()
                    Text("\(card.expiryMonth)/\(String(card.expiryYear).suffix(2))")
                        .font(.poppins(weight: .regular, size: 12))
                        .foregroundColor(.secondary)
                }
            }
            .padding(12)
            .frame(width: 200, height: 160)
            .background(Color(.secondarySystemBackground))
            .cornerRadius(12)
            .overlay(
                RoundedRectangle(cornerRadius: 12)
                    .stroke(isSelected ? Color.green : Color.clear, lineWidth: 2)
            )
        }
        .buttonStyle(.plain)
    }
}

// MARK: - New Card Input
struct NewCardInputSection: View {
    @Binding var cardModel: CardInputModel

    var body: some View {
        VStack(spacing: 16) {
            Text("Yeni Kart Bilgileri")
                .font(.poppins(weight: .bold, size: 16))
                .foregroundColor(.primary)
                .frame(maxWidth: .infinity, alignment: .leading)

            // Card Number
            VStack(alignment: .leading, spacing: 6) {
                Text("Kart Numarası")
                    .font(.poppins(weight: .semibold, size: 12))
                    .foregroundColor(.secondary)

                TextField("4111 1111 1111 1111", text: $cardModel.cardNumber)
                    .keyboardType(.numberPad)
                    .textContentType(.creditCardNumber)
                    .padding(12)
                    .background(Color(.tertiarySystemBackground))
                    .cornerRadius(8)
                    .onChange(of: cardModel.cardNumber) { _, newValue in
                        cardModel.cardNumber = formatCardNumber(newValue)
                    }
            }

            // Cardholder Name
            VStack(alignment: .leading, spacing: 6) {
                Text("Kart Sahibinin Adı")
                    .font(.poppins(weight: .semibold, size: 12))
                    .foregroundColor(.secondary)

                TextField("AHMET YILMAZ", text: $cardModel.cardholderName)
                    .textContentType(.name)
                    .padding(12)
                    .background(Color(.tertiarySystemBackground))
                    .cornerRadius(8)
            }

            HStack(spacing: 12) {
                // Expiry
                VStack(alignment: .leading, spacing: 6) {
                    Text("Ay / Yıl")
                        .font(.poppins(weight: .semibold, size: 12))
                        .foregroundColor(.secondary)

                    HStack(spacing: 8) {
                        TextField("MM", text: $cardModel.expiryMonth)
                            .keyboardType(.numberPad)
                            .frame(width: 40)
                            .padding(10)
                            .background(Color(.tertiarySystemBackground))
                            .cornerRadius(6)

                        Text("/")
                            .foregroundColor(.secondary)

                        TextField("YY", text: $cardModel.expiryYear)
                            .keyboardType(.numberPad)
                            .frame(width: 40)
                            .padding(10)
                            .background(Color(.tertiarySystemBackground))
                            .cornerRadius(6)
                    }
                }

                // CVV
                VStack(alignment: .leading, spacing: 6) {
                    Text("CVV")
                        .font(.poppins(weight: .semibold, size: 12))
                        .foregroundColor(.secondary)

                    TextField("•••", text: $cardModel.cvv)
                        .keyboardType(.numberPad)
                        .textContentType(.creditCardSecurityCode)
                        .frame(width: 60)
                        .padding(10)
                        .background(Color(.tertiarySystemBackground))
                        .cornerRadius(6)
                }

                Spacer()
            }

            HStack(spacing: 8) {
                Image(systemName: "lock.fill")
                    .font(.system(size: 12))
                    .foregroundColor(.green)

                Text("3D Secure korumalı ödeme")
                    .font(.poppins(weight: .regular, size: 12))
                    .foregroundColor(.secondary)

                Spacer()
            }
        }
    }

    private func formatCardNumber(_ value: String) -> String {
        let cleaned = value.replacingOccurrences(of: " ", with: "").prefix(16)
        var formatted = ""

        for (index, char) in cleaned.enumerated() {
            if index > 0 && index % 4 == 0 {
                formatted.append(" ")
            }
            formatted.append(char)
        }

        return formatted
    }
}
