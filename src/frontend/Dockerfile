# 1. Gunakan imej rasmi Node.js 20 berasaskan Alpine Linux (sangat ringan)
FROM node:20-alpine

# 2. Tetapkan folder kerja di dalam container
WORKDIR /app

# 3. Salin fail pakej dahulu untuk manfaatkan Docker cache
COPY package*.json ./

# 4. Pasang semua pakej Node.js
RUN npm install

# 5. Salin keseluruhan kod projek frontend
COPY . .

# 6. Buka port 3000 untuk diakses dari browser luar
EXPOSE 3000

# 7. Hidupkan server Nuxt (0.0.0.0 membenarkan akses dari luar container)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]