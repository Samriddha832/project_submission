<footer class="footer">
    <div class="footer-inner">

        <div class="footer-col">
            <h4>HotelBooking</h4>
            <p>&copy; <?php echo date("Y"); ?> Suman & Samriddha</p>
        </div>

        <div class="footer-col">
            <h4>Contact Us</h4>
            <p>📧 Suman123@gmail.com</p>
            <p>📧 Samriddha123@gmail.com</p>
            <p>📞 9810000000</p>
            <p>📞 9820000000</p>
        </div>

        <div class="footer-col">
            <h4>Payment Methods</h4>
            <div class="payment-icons">
                <img src="/Hotel-Booking/uploads/esewa.png" class="pay-logo">
                <img src="/Hotel-Booking/uploads/khalti.png" class="pay-logo">
            </div>
        </div>

    </div>
</footer>

<style>
.footer  {
    background: #F5F1DC;
    padding: 40px 10px;
    margin-top: 40px;
}

.footer-inner {
    width: 90%;
    max-width: 1100px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.footer-col {
    width: 30%;
    min-width: 200px;
    margin-bottom: 25px;
}

.footer-col h4 {
    margin-bottom: 10px;
    font-size: 18px;
    font-weight: 600;
    color: black;
}

.footer-col p {
    color: black;
    font-size: 15px;
    margin: 5px 0;
}

.payment-icons {
    margin-top: 10px;
}

.pay-logo {
    height: 40px;
    margin-right: 12px;
    transition: 0.25s;
}

.pay-logo:hover {
    transform: scale(1.1);
}

/* MOBILE */
@media(max-width: 600px) {
    .footer-inner {
        text-align: center;
    }

    .footer-col {
        width: 100%;
    }
}
</style>
