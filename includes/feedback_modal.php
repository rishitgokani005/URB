<!-- Feedback Modal -->
<div id="feedbackModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center; z-index: 3100; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px);">
    <div class="modal-content" style="position: relative; width: 90%; max-width: 450px; padding: 3rem; border-radius: 30px; background: white; box-shadow: 0 30px 60px rgba(0,0,0,0.2); border: 1px solid rgba(0,0,0,0.05); animation: fadeInUp 0.5s ease;">
        <button class="close-modal" id="closeFeedbackModal" style="position: absolute; top: 25px; right: 25px; background: #F1F5F9; border: none; color: #64748B; width: 40px; height: 40px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center;" onclick="hideFeedbackModal()">&times;</button>

        <h2 style="font-family: var(--font-heading); color: var(--text-main); font-size: 1.8rem; margin-bottom: 0.5rem; text-align: center;">Rate Your Ride</h2>
        <p style="text-align: center; color: var(--text-sub); margin-bottom: 2rem;">Share your experience to help us improve.</p>

        <form action="<?php echo $base_url; ?>Taxi-Booking/submit_feedback.php" method="POST" id="feedbackForm">
            <input type="hidden" name="booking_id" id="feedback_booking_id">
            <input type="hidden" name="cab_id" id="feedback_cab_id">
            <input type="hidden" name="rating" id="feedback_rating_value" value="5">

            <!-- Star Rating Input -->
            <div style="display: flex; justify-content: center; gap: 12px; margin-bottom: 2rem;">
                <i class="fas fa-star star-btn" data-index="1" style="font-size: 2.2rem; color: #FFD700; cursor: pointer; transition: transform 0.2s;"></i>
                <i class="fas fa-star star-btn" data-index="2" style="font-size: 2.2rem; color: #FFD700; cursor: pointer; transition: transform 0.2s;"></i>
                <i class="fas fa-star star-btn" data-index="3" style="font-size: 2.2rem; color: #FFD700; cursor: pointer; transition: transform 0.2s;"></i>
                <i class="fas fa-star star-btn" data-index="4" style="font-size: 2.2rem; color: #FFD700; cursor: pointer; transition: transform 0.2s;"></i>
                <i class="fas fa-star star-btn" data-index="5" style="font-size: 2.2rem; color: #FFD700; cursor: pointer; transition: transform 0.2s;"></i>
            </div>

            <!-- Comments Textarea -->
            <div style="margin-bottom: 2rem;">
                <textarea name="comments" placeholder="Write your comments here... (optional)" rows="4" style="width: 100%; padding: 15px; border-radius: 15px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s; font-family: 'Inter', sans-serif; font-size: 0.95rem; resize: none;"></textarea>
            </div>

            <button type="submit" class="btn-signup" style="width: 100%; padding: 15px; border: none; cursor: pointer; font-size: 1.1rem; border-radius: 15px; background: var(--primary); color: white; font-weight: 700; transition: 0.3s;">Submit Feedback</button>
        </form>
    </div>
</div>

<script>
    const feedbackModal = document.getElementById('feedbackModal');
    const stars = document.querySelectorAll('.star-btn');
    const ratingValueInput = document.getElementById('feedback_rating_value');

    function showFeedbackModal(bookingId, cabId) {
        document.getElementById('feedback_booking_id').value = bookingId;
        document.getElementById('feedback_cab_id').value = cabId;
        // Reset stars to 5 by default
        setRating(5);
        feedbackModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideFeedbackModal() {
        feedbackModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function setRating(rating) {
        ratingValueInput.value = rating;
        stars.forEach(star => {
            const index = parseInt(star.getAttribute('data-index'));
            if (index <= rating) {
                star.className = 'fas fa-star star-btn';
                star.style.color = '#FFD700'; // Filled Gold
            } else {
                star.className = 'far fa-star star-btn';
                star.style.color = '#CBD5E1'; // Outline Slate-300
            }
        });
    }

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = parseInt(star.getAttribute('data-index'));
            setRating(rating);
        });
        star.addEventListener('mouseenter', () => {
            star.style.transform = 'scale(1.2)';
        });
        star.addEventListener('mouseleave', () => {
            star.style.transform = 'scale(1)';
        });
    });

    window.onclick = function(event) {
        if (event.target == feedbackModal) {
            hideFeedbackModal();
        }
    }
</script>
