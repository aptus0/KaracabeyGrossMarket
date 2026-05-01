import SwiftUI

struct ProductReviewsView: View {
    let productSlug: String
    @State private var reviews: [Review] = []
    @State private var isLoading = false
    @State private var showCreateReview = false
    @State private var averageRating: Double = 0

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(alignment: .leading, spacing: 20) {
                    // Rating Summary
                    VStack(alignment: .leading, spacing: 12) {
                        Text("Değerlendirmeler")
                            .font(.poppins(weight: .bold, size: 18))
                            .foregroundColor(.primary)

                        HStack(spacing: 16) {
                            VStack(alignment: .center, spacing: 4) {
                                Text(String(format: "%.1f", averageRating))
                                    .font(.poppins(weight: .bold, size: 32))
                                    .foregroundColor(.kgmOrange)

                                HStack(spacing: 2) {
                                    ForEach(0..<5, id: \.self) { i in
                                        Image(systemName: i < Int(averageRating) ? "star.fill" : "star")
                                            .font(.system(size: 12))
                                            .foregroundColor(.kgmOrange)
                                    }
                                }
                            }
                            .frame(width: 80)

                            Spacer()

                            VStack(alignment: .trailing, spacing: 6) {
                                ForEach([5, 4, 3, 2, 1], id: \.self) { rating in
                                    HStack(spacing: 8) {
                                        Text("\(rating)★")
                                            .font(.poppins(weight: .regular, size: 11))
                                            .foregroundColor(.secondary)
                                            .frame(width: 30)

                                        ProgressView(value: Double(rating) / 5)
                                            .frame(width: 80)

                                        Text("\(reviews.filter { $0.rating == rating }.count)")
                                            .font(.poppins(weight: .regular, size: 11))
                                            .foregroundColor(.secondary)
                                            .frame(width: 30, alignment: .trailing)
                                    }
                                }
                            }
                        }
                    }
                    .padding()
                    .background(Color(.secondarySystemBackground))
                    .cornerRadius(12)

                    // Create Review Button
                    Button(action: { showCreateReview = true }) {
                        HStack {
                            Image(systemName: "pencil.and.list.clipboard")
                            Text("Değerlendirme Yap")
                        }
                        .font(.poppins(weight: .semibold, size: 14))
                        .frame(maxWidth: .infinity)
                        .padding(12)
                        .background(Color.kgmOrange)
                        .foregroundColor(.white)
                        .cornerRadius(10)
                    }

                    // Reviews List
                    if isLoading {
                        ProgressView()
                            .frame(maxWidth: .infinity, alignment: .center)
                    } else if reviews.isEmpty {
                        Text("Henüz değerlendirme yok")
                            .font(.poppins(weight: .regular, size: 14))
                            .foregroundColor(.secondary)
                            .frame(maxWidth: .infinity, alignment: .center)
                            .padding()
                    } else {
                        ForEach(reviews) { review in
                            ReviewCard(review: review)
                        }
                    }

                    Spacer().frame(height: 20)
                }
                .padding()
            }
            .navigationDestination(isPresented: $showCreateReview) {
                CreateReviewView(productSlug: productSlug)
            }
            .task {
                await loadReviews()
            }
        }
    }

    private func loadReviews() async {
        isLoading = true
        // API call will be added here
        isLoading = false
    }
}

struct ReviewCard: View {
    let review: Review

    var body: some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack {
                VStack(alignment: .leading, spacing: 4) {
                    Text(review.title)
                        .font(.poppins(weight: .semibold, size: 14))
                        .foregroundColor(.primary)

                    HStack(spacing: 2) {
                        ForEach(0..<5, id: \.self) { i in
                            Image(systemName: i < review.rating ? "star.fill" : "star")
                                .font(.system(size: 12))
                                .foregroundColor(.kgmOrange)
                        }
                    }
                }

                Spacer()

                if review.verified {
                    Label("Doğrulandı", systemImage: "checkmark.seal.fill")
                        .font(.poppins(weight: .semibold, size: 10))
                        .foregroundColor(.green)
                }
            }

            if let comment = review.comment {
                Text(comment)
                    .font(.poppins(weight: .regular, size: 13))
                    .foregroundColor(.secondary)
                    .lineSpacing(2)
            }

            HStack {
                Text(review.userName)
                    .font(.poppins(weight: .semibold, size: 12))
                    .foregroundColor(.primary)

                Spacer()

                Text(review.createdAt ?? "")
                    .font(.poppins(weight: .regular, size: 11))
                    .foregroundColor(.secondary)

                if review.helpfulCount > 0 {
                    Label(String(review.helpfulCount), systemImage: "hand.thumbsup.fill")
                        .font(.poppins(weight: .regular, size: 11))
                        .foregroundColor(.secondary)
                }
            }
        }
        .padding()
        .background(Color(.secondarySystemBackground))
        .cornerRadius(10)
    }
}

struct CreateReviewView: View {
    let productSlug: String
    @Environment(\.dismiss) var dismiss
    @State private var rating: Int = 5
    @State private var title = ""
    @State private var comment = ""
    @State private var isSubmitting = false

    var body: some View {
        NavigationStack {
            Form {
                Section("Puan") {
                    HStack(spacing: 16) {
                        ForEach(1...5, id: \.self) { rate in
                            Button(action: { rating = rate }) {
                                Image(systemName: rate <= rating ? "star.fill" : "star")
                                    .font(.system(size: 24))
                                    .foregroundColor(rate <= rating ? .kgmOrange : .gray)
                            }
                        }
                        Spacer()
                    }
                }

                Section("Başlık") {
                    TextField("Başlık", text: $title)
                }

                Section("Yorum") {
                    TextEditor(text: $comment)
                        .frame(height: 100)
                }

                Section {
                    Button(action: submitReview) {
                        if isSubmitting {
                            ProgressView()
                                .frame(maxWidth: .infinity, alignment: .center)
                        } else {
                            Text("Gönder")
                                .frame(maxWidth: .infinity)
                        }
                    }
                    .disabled(title.isEmpty || isSubmitting)
                }
            }
            .navigationTitle("Değerlendirme Yap")
            .navigationBarTitleDisplayMode(.inline)
        }
    }

    private func submitReview() {
        isSubmitting = true
        Task {
            defer { isSubmitting = false }
            // API call will be added here
            dismiss()
        }
    }
}
