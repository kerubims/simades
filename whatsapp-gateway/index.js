const express = require('express');
const cors = require('cors');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const pino = require('pino');
const QRCode = require('qrcode');
const fs = require('fs');

const app = express();
const port = 3001;

app.use(cors());
app.use(express.json({ limit: '50mb' }));

let sock;
let qrCodeData = null;
let connectionStatus = 'connecting';
let connectedNumber = null;

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
    const { version, isLatest } = await fetchLatestBaileysVersion();
    console.log(`Using WA v${version.join('.')}, isLatest: ${isLatest}`);

    sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: true,
        logger: pino({ level: 'silent' }),
        browser: ['SIMADES Gateway', 'Chrome', '1.0.0'],
    });

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            qrCodeData = await QRCode.toDataURL(qr);
            connectionStatus = 'qr_ready';
        }

        if (connection === 'close') {
            qrCodeData = null;
            connectedNumber = null;
            
            const statusCode = lastDisconnect.error?.output?.statusCode;
            const isLoggedOut = statusCode === DisconnectReason.loggedOut;
            const shouldReconnect = !isLoggedOut;
            
            console.log('Connection closed. Status code:', statusCode, 'Reconnecting:', shouldReconnect);

            if (shouldReconnect) {
                connectionStatus = 'connecting';
                // Wait a bit before reconnecting to avoid spamming
                setTimeout(connectToWhatsApp, 3000);
            } else {
                connectionStatus = 'logged_out';
                console.log('Logged out detected. Clearing credentials and preparing for new QR scan...');
                
                // Clean up old socket event listeners to avoid duplicates and leaks
                if (sock) {
                    try {
                        sock.ev.removeAllListeners('connection.update');
                        sock.ev.removeAllListeners('creds.update');
                    } catch (err) {
                        console.log('Error removing listeners:', err.message);
                    }
                }

                // Wait 1.5 seconds to allow Baileys to release file locks on Windows/Linux
                setTimeout(() => {
                    try {
                        if (fs.existsSync('auth_info_baileys')) {
                            fs.rmSync('auth_info_baileys', { recursive: true, force: true });
                        }
                        if (fs.existsSync('sessions')) {
                            fs.rmSync('sessions', { recursive: true, force: true });
                        }
                        console.log('Auth credentials cleared successfully.');
                    } catch (e) {
                        console.log('Could not remove auth folder:', e.message);
                    }
                    // Reinitialize a clean socket which will generate a new QR code
                    connectToWhatsApp();
                }, 1500);
            }
        } else if (connection === 'open') {
            console.log('opened connection');
            connectionStatus = 'connected';
            qrCodeData = null;
            connectedNumber = sock.user.id.split(':')[0];
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

connectToWhatsApp();

app.get('/status', (req, res) => {
    res.json({
        connected: connectionStatus === 'connected',
        status: connectionStatus,
        number: connectedNumber,
        message: connectionStatus === 'connected' ? 'OK' : 'Waiting or disconnected',
    });
});

app.get('/qr', (req, res) => {
    if (connectionStatus === 'connected') {
        return res.json({ qr: null, message: 'Sudah terhubung dengan WhatsApp' });
    }
    
    if (qrCodeData) {
        return res.json({ qr: qrCodeData, message: 'QR Code siap di-scan' });
    }

    return res.json({ qr: null, message: 'Menunggu QR Code...' });
});

app.post('/logout', async (req, res) => {
    if (sock && connectionStatus === 'connected') {
        await sock.logout();
        res.json({ success: true, message: 'Logged out successfully' });
    } else {
        res.json({ success: false, message: 'Not connected' });
    }
});

app.post('/send-document', async (req, res) => {
    if (connectionStatus !== 'connected') {
        return res.status(503).json({ success: false, message: 'WhatsApp tidak terhubung' });
    }

    const { to, base64, filename, caption } = req.body;

    if (!to || !base64) {
        return res.status(400).json({ success: false, message: 'Parameter "to" dan "base64" wajib diisi' });
    }

    try {
        let jid = to;
        // Hapus karakter non-digit
        jid = jid.replace(/\D/g, '');
        if (!jid.includes('@')) {
            jid = `${jid}@s.whatsapp.net`;
        }

        const buffer = Buffer.from(base64, 'base64');

        await sock.sendMessage(jid, {
            document: buffer,
            mimetype: 'application/pdf',
            fileName: filename || 'document.pdf',
            caption: caption || '',
        });

        res.json({ success: true, message: 'Dokumen berhasil dikirim' });
    } catch (error) {
        console.error('Error sending document:', error);
        res.status(500).json({ success: false, message: 'Gagal mengirim dokumen', error: error.message });
    }
});

app.listen(port, () => {
    console.log(`WhatsApp Gateway running at http://localhost:${port}`);
});
