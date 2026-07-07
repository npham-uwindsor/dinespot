<!-- Reusable confirmation dialog for destructive actions (cancel, delete) -->
<div
    id="confirm-modal"
    class="modal-overlay confirm-modal"
    role="presentation"
    hidden
>
    <div
        class="modal-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
        aria-describedby="confirm-modal-message"
    >
        <h2 id="confirm-modal-title">Confirm Action</h2>
        <p id="confirm-modal-message" class="modal-lead"></p>
        <form id="confirm-modal-form" method="post" action="">
            <input type="hidden" name="id" id="confirm-modal-id" value="">
            <input type="hidden" name="redirect" id="confirm-modal-redirect" value="">
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary" id="confirm-modal-submit">Confirm</button>
                <button type="button" class="btn btn-secondary" id="confirm-modal-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>
