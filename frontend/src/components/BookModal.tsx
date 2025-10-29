import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { X, ShoppingCart, CheckCircle, Package } from 'lucide-react';
import { Country, State, City } from 'country-state-city';
import { mediaPath } from '../lib/config';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from './ui/select';
import { Book } from '../pages/Store';

interface BookModalProps {
  book: Book;
  onClose: () => void;
}

export function BookModal({ book, onClose }: BookModalProps) {
  const [showShippingForm, setShowShippingForm] = useState(false);
  const [shippingData, setShippingData] = useState({
    fullName: '',
    phone: '',
    address: '',
    country: '',
    state: '',
    city: '',
  });

  const [selectedCountryCode, setSelectedCountryCode] = useState('');
  const [selectedStateCode, setSelectedStateCode] = useState('');
  const [availableStates, setAvailableStates] = useState<any[]>([]);
  const [availableCities, setAvailableCities] = useState<any[]>([]);
  const [showCityInput, setShowCityInput] = useState(false);

  const countries = Country.getAllCountries();

  useEffect(() => {
    if (selectedCountryCode) {
      const states = State.getStatesOfCountry(selectedCountryCode);
      setAvailableStates(states);
      setSelectedStateCode('');
      setAvailableCities([]);
      setShippingData((prev) => ({ ...prev, state: '', city: '' }));
    } else {
      setAvailableStates([]);
      setAvailableCities([]);
    }
  }, [selectedCountryCode]);

  useEffect(() => {
    if (selectedCountryCode && selectedStateCode) {
      const cities = City.getCitiesOfState(selectedCountryCode, selectedStateCode);
      setAvailableCities(cities);

      if (cities.length === 0) {
        setShowCityInput(true);
      } else {
        setShowCityInput(false);
        setShippingData((prev) => ({ ...prev, city: '' }));
      }
    } else {
      setAvailableCities([]);
      setShowCityInput(false);
    }
  }, [selectedCountryCode, selectedStateCode]);

  const handleShippingChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setShippingData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleCountryChange = (value: string) => {
    const country = countries.find((c) => c.isoCode === value);
    if (country) {
      setSelectedCountryCode(value);
      setShippingData((prev) => ({
        ...prev,
        country: country.name,
        state: '',
        city: '',
      }));
    }
  };

  const handleStateChange = (value: string) => {
    const state = availableStates.find((s) => s.isoCode === value);
    if (state) {
      setSelectedStateCode(value);
      setShippingData((prev) => ({
        ...prev,
        state: state.name,
        city: '',
      }));
    }
  };

  const handleCityChange = (value: string) => {
    setShippingData((prev) => ({
      ...prev,
      city: value,
    }));
  };

  const handleBuyNow = () => {
    if (book.type === 'physical') {
      setShowShippingForm(true);
    } else {
      handleProceedToPayment();
    }
  };

  const handleProceedToPayment = () => {
    alert(
      `Redirecting to payment gateway...\n\nIn production, this would redirect to Paystack/Flutterwave.\n\nShipping details:\n${JSON.stringify(
        shippingData,
        null,
        2
      )}`
    );
    onClose();
  };

  return (
    <AnimatePresence>
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        onClick={onClose}
      >
        <motion.div
          initial={{ scale: 0.9, opacity: 0 }}
          animate={{ scale: 1, opacity: 1 }}
          exit={{ scale: 0.9, opacity: 0 }}
          transition={{ duration: 0.3 }}
          className="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl"
          onClick={(e) => e.stopPropagation()}
        >
          {/* Header */}
          <div className="sticky top-0 bg-white dark:bg-slate-900 z-10 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
            <h2 className="text-xl font-bold text-slate-900 dark:text-white">
              Book Details
            </h2>
            <button
              onClick={onClose}
              className="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"
              aria-label="Close modal"
            >
              <X className="w-5 h-5 text-slate-600 dark:text-slate-400" />
            </button>
          </div>

          {/* Book Details */}
          <div className="p-6">
            <div className="aspect-[3/4] max-w-sm mx-auto rounded-2xl mb-8 flex items-center justify-center relative overflow-hidden shadow-lg">
              {book.cover_image ? (
                <img
                  src={mediaPath(book.cover_image)}
                  alt={book.title}
                  className="w-full h-full object-cover rounded-2xl"
                />
              ) : (
                <div className="w-full h-full flex items-center justify-center bg-[#D4AF37] rounded-2xl">
                  <ShoppingCart className="w-12 h-12 text-white" />
                </div>
              )}
              <div className="absolute top-4 right-4 bg-[#D4AF37] text-white px-3 py-1 rounded-full text-xs font-bold uppercase">
                {book.type}
              </div>
            </div>

            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-3xl font-bold text-slate-900 dark:text-white mb-2">
                  {book.title}
                </h3>
                {book.subtitle && (
                  <p className="text-lg font-medium text-[#D4AF37] dark:text-[#E5C068] mb-4">
                    {book.subtitle}
                  </p>
                )}
                <p className="text-3xl font-bold text-slate-900 dark:text-white">
                  {book.currency === 'NGN' ? '₦' : '$'}
                  {book.price.toLocaleString()}
                </p>
              </div>

              {/* About */}
              <div className="border-t border-slate-200 dark:border-slate-700 pt-6">
                <h4 className="text-xl font-bold text-slate-900 dark:text-white mb-3">
                  About This Book
                </h4>
                <p className="text-slate-700 dark:text-slate-300 leading-relaxed mb-6">
                  {book.detailedDescription || book.description}
                </p>

                {book.keyLessons && book.keyLessons.length > 0 && (
                  <div>
                    <h4 className="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                      <CheckCircle className="w-5 h-5 text-[#D4AF37]" />
                      Key Lessons
                    </h4>
                    <ul className="space-y-3">
                      {book.keyLessons.map((lesson, i) => (
                        <li key={i} className="flex items-start gap-3">
                          <div className="w-2 h-2 rounded-full bg-[#D4AF37] mt-2 flex-shrink-0"></div>
                          <span className="text-slate-700 dark:text-slate-300 leading-relaxed">
                            {lesson}
                          </span>
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
              </div>

              {/* Shipping Form or Buy Button */}
              {!showShippingForm ? (
                <div className="border-t border-slate-200 dark:border-slate-700 pt-6">
                  <Button
                    onClick={handleBuyNow}
                    className="w-full bg-[#D4AF37] hover:bg-[#c39f31] text-black font-bold py-6 text-lg rounded-xl transition-all duration-300"
                  >
                    <ShoppingCart className="w-5 h-5 mr-2" />
                    {book.type === 'physical' ? 'Proceed to Checkout' : 'Buy Now'}
                  </Button>
                </div>
              ) : (
                <>
                  {/* ✅ Shipping form section (fully working) */}
                  <div className="border-t border-slate-200 dark:border-slate-700 pt-6 space-y-6">
                    <div className="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6 flex items-start gap-3">
                      <Package className="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" />
                      <div>
                        <h5 className="font-semibold text-amber-900 dark:text-amber-300 mb-1">
                          Shipping Information Required
                        </h5>
                        <p className="text-sm text-amber-700 dark:text-amber-400">
                          Please provide your delivery address to complete your order.
                        </p>
                      </div>
                    </div>

                    <form
                      className="space-y-4"
                      onSubmit={(e) => {
                        e.preventDefault();
                        handleProceedToPayment();
                      }}
                    >
                      {/* Full Name */}
                      <div>
                        <Label htmlFor="fullName">Full Name *</Label>
                        <Input
                          id="fullName"
                          name="fullName"
                          type="text"
                          required
                          value={shippingData.fullName}
                          onChange={handleShippingChange}
                          placeholder="Your full name"
                        />
                      </div>

                      {/* Phone */}
                      <div>
                        <Label htmlFor="phone">Phone *</Label>
                        <Input
                          id="phone"
                          name="phone"
                          type="tel"
                          required
                          value={shippingData.phone}
                          onChange={handleShippingChange}
                          placeholder="+234 XXX XXX XXXX"
                        />
                      </div>

                      {/* Address */}
                      <div>
                        <Label htmlFor="address">Address *</Label>
                        <Input
                          id="address"
                          name="address"
                          type="text"
                          required
                          value={shippingData.address}
                          onChange={handleShippingChange}
                          placeholder="Street address"
                        />
                      </div>

                      {/* Country / State / City */}
                      <div>
                        <Label htmlFor="country">Country *</Label>
                        <Select value={selectedCountryCode} onValueChange={handleCountryChange}>
                          <SelectTrigger>
                            <SelectValue placeholder="Select country" />
                          </SelectTrigger>
                          <SelectContent>
                            {countries.map((c) => (
                              <SelectItem key={c.isoCode} value={c.isoCode}>
                                {c.name}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                      </div>

                      <div>
                        <Label htmlFor="state">State *</Label>
                        <Select
                          value={selectedStateCode}
                          onValueChange={handleStateChange}
                          disabled={!selectedCountryCode || availableStates.length === 0}
                        >
                          <SelectTrigger>
                            <SelectValue placeholder="Select state" />
                          </SelectTrigger>
                          <SelectContent>
                            {availableStates.map((s) => (
                              <SelectItem key={s.isoCode} value={s.isoCode}>
                                {s.name}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                      </div>

                      <div>
                        <Label htmlFor="city">City *</Label>
                        {showCityInput || availableCities.length === 0 ? (
                          <Input
                            id="city"
                            name="city"
                            type="text"
                            required
                            value={shippingData.city}
                            onChange={handleShippingChange}
                            placeholder="Enter city"
                          />
                        ) : (
                          <Select value={shippingData.city} onValueChange={handleCityChange}>
                            <SelectTrigger>
                              <SelectValue placeholder="Select city" />
                            </SelectTrigger>
                            <SelectContent>
                              {availableCities.map((c) => (
                                <SelectItem key={c.name} value={c.name}>
                                  {c.name}
                                </SelectItem>
                              ))}
                            </SelectContent>
                          </Select>
                        )}
                      </div>

                      <Button
                        type="submit"
                        className="w-full bg-[#D4AF37] hover:bg-[#c39f31] text-black font-bold py-6 text-lg rounded-xl transition-all duration-300"
                      >
                        Proceed to Payment
                      </Button>
                    </form>
                  </div>
                </>
              )}
            </div>
          </div>
        </motion.div>
      </motion.div>
    </AnimatePresence>
  );
}
